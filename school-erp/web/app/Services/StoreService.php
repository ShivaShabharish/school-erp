<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\StoreItem;
use App\Models\StoreSale;
use App\Models\StoreSaleItem;
use App\Models\Student;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class StoreService
{
    /**
     * Generate sequential and unique Store Bill Number.
     * Pattern: SCH/{AY-Short}/STR-{00001}
     */
    public function generateBillNumber(?AcademicYear $ay = null): string
    {
        $ay = $ay ?? AcademicYear::current() ?? AcademicYear::first();
        $yearSlug = $ay ? str_replace('20', '', explode('-', $ay->name)[0]) . '-' . str_replace('20', '', explode('-', $ay->name)[1] ?? '27') : '26-27';

        $lastBill = StoreSale::where('bill_no', 'like', "SCH/{$yearSlug}/STR-%")
            ->orderByDesc('id')
            ->first();

        $nextSeq = 1;
        if ($lastBill && preg_match('/STR-(\d+)$/', $lastBill->bill_no, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        }

        return sprintf("SCH/%s/STR-%05d", $yearSlug, $nextSeq);
    }

    /**
     * Process Point-of-Sale (POS) store transaction.
     * Decrements inventory stock, computes cost & profit, inside DB transaction.
     */
    public function processSale(array $data, User $cashier): StoreSale
    {
        $itemsData = $data['items'] ?? [];
        if (empty($itemsData)) {
            throw new InvalidArgumentException('Store sale must contain at least one item.');
        }

        return DB::transaction(function () use ($data, $itemsData, $cashier) {
            $ay = AcademicYear::current() ?? AcademicYear::first();
            $billNo = $this->generateBillNumber($ay);

            $student = !empty($data['student_id']) ? Student::find($data['student_id']) : null;
            $customerName = $data['customer_name'] ?? ($student ? $student->full_name : 'Walk-in Student/Parent');
            $customerType = $data['customer_type'] ?? ($student ? 'student' : 'visitor');
            $paymentMethod = $data['payment_method'] ?? 'Cash';
            $discount = (float) ($data['discount'] ?? 0);

            $totalSubtotal = 0;
            $totalCost = 0;
            $preparedSaleItems = [];

            foreach ($itemsData as $entry) {
                $itemId = $entry['store_item_id'];
                $qty = (int) $entry['quantity'];

                if ($qty <= 0) {
                    continue;
                }

                // Lock row for update to ensure atomic stock decrement
                $storeItem = StoreItem::where('id', $itemId)->lockForUpdate()->firstOrFail();

                if ($storeItem->stock_quantity < $qty) {
                    throw new InvalidArgumentException(
                        "Insufficient stock for item: {$storeItem->name}. Available: {$storeItem->stock_quantity}, Requested: {$qty}."
                    );
                }

                $unitPrice = isset($entry['unit_price']) ? (float) $entry['unit_price'] : (float) $storeItem->selling_price;
                $costPrice = (float) $storeItem->purchase_price;
                $subtotal = round($unitPrice * $qty, 2);
                $itemCost = round($costPrice * $qty, 2);
                $itemProfit = round($subtotal - $itemCost, 2);

                $totalSubtotal += $subtotal;
                $totalCost += $itemCost;

                // Decrement inventory stock
                $storeItem->stock_quantity -= $qty;
                $storeItem->save();

                $preparedSaleItems[] = [
                    'store_item' => $storeItem,
                    'quantity' => $qty,
                    'cost_price' => $costPrice,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'profit' => $itemProfit,
                ];
            }

            if (empty($preparedSaleItems)) {
                throw new InvalidArgumentException('No valid items processed for sale.');
            }

            $totalAmount = max(0, $totalSubtotal - $discount);
            $totalProfit = max(0, $totalAmount - $totalCost);

            $sale = StoreSale::create([
                'bill_no' => $billNo,
                'student_id' => $student?->id,
                'customer_name' => $customerName,
                'customer_type' => $customerType,
                'subtotal' => $totalSubtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'cost_amount' => $totalCost,
                'profit_amount' => $totalProfit,
                'payment_method' => $paymentMethod,
                'sale_date' => $data['sale_date'] ?? now()->toDateString(),
                'cashier_id' => $cashier->id,
                'notes' => $data['notes'] ?? 'School store POS purchase',
            ]);

            foreach ($preparedSaleItems as $pItem) {
                StoreSaleItem::create([
                    'store_sale_id' => $sale->id,
                    'store_item_id' => $pItem['store_item']->id,
                    'quantity' => $pItem['quantity'],
                    'cost_price' => $pItem['cost_price'],
                    'unit_price' => $pItem['unit_price'],
                    'subtotal' => $pItem['subtotal'],
                    'profit' => $pItem['profit'],
                ]);
            }

            AuditLog::record('store_sale_completed', $sale, null, [
                'bill_no' => $sale->bill_no,
                'total_amount' => $sale->total_amount,
                'items_count' => count($preparedSaleItems),
                'profit' => $sale->profit_amount,
            ]);

            return $sale;
        });
    }

    /**
     * Restock an inventory item.
     */
    public function restockItem(StoreItem $item, int $quantity, ?float $purchasePrice = null): void
    {
        DB::transaction(function () use ($item, $quantity, $purchasePrice) {
            $item->stock_quantity += $quantity;
            if ($purchasePrice !== null && $purchasePrice > 0) {
                $item->purchase_price = $purchasePrice;
            }
            $item->save();

            AuditLog::record('store_item_restocked', $item, null, [
                'item_code' => $item->item_code,
                'added_quantity' => $quantity,
                'new_stock' => $item->stock_quantity,
            ]);
        });
    }

    /**
     * Generate official DomPDF Store Cash Memo / Receipt.
     */
    public function generateStoreReceiptPdf(StoreSale $sale)
    {
        $sale->load(['cashier', 'items.storeItem', 'student.schoolClass', 'student.section']);

        $pdf = Pdf::loadView('accounts.store.receipt_pdf', compact('sale'))
            ->setPaper('a5', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf;
    }
}
