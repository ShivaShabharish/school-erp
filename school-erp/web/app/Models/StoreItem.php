<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'item_code',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'alert_threshold',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'alert_threshold' => 'integer',
        ];
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->alert_threshold;
    }
}
