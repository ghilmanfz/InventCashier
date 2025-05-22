<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function updateQuantityProductStock(): void
    {
        $product = $this->product;
        $product->stock_quantity -= $this->getOriginal('quantity');
        $product->stock_quantity += $this->quantity;
        $product->save();
    }
}
