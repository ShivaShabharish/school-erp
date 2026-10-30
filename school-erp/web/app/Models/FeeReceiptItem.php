<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_receipt_id',
        'fee_allocation_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(FeeReceipt::class, 'fee_receipt_id');
    }

    public function feeAllocation(): BelongsTo
    {
        return $this->belongsTo(FeeAllocation::class, 'fee_allocation_id');
    }
}
