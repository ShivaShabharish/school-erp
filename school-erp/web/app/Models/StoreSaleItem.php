<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_sale_id',
        'store_item_id',
        'quantity',
        'cost_price',
        'unit_price',
        'subtotal',
        'profit',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(StoreSale::class, 'store_sale_id');
    }

    public function storeItem(): BelongsTo
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }
}
