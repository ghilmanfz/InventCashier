<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'status'         => \App\Enums\OrderStatus::class,
        'payment_method' => \App\Enums\PaymentMethod::class,
    ];

    /**
     * Boot model observers.
     */
    protected static function booted(): void
    {
        // set user & init total saat membuat Order
        static::creating(function (self $order) {
            $order->user_id = auth()->id();
            $order->total   = 0;
        });

        // hitung ulang subtotal, total, profit SETIAP save
        static::saving(function (self $order) {
            // pastikan relasi & cost_price produk sudah dimuat
            $order->loadMissing('orderDetails.product');

            // subtotal semua item
            $subtotal      = $order->orderDetails->sum('subtotal');
            $order->total  = max($subtotal - ($order->discount ?? 0), 0);

            // profit = selisih harga jual - HPP
            $order->profit = $order->orderDetails->reduce(
                fn (int $carry, $detail) =>
                    $carry + (($detail->price - $detail->product->cost_price) * $detail->quantity),
                0
            );
        });
    }

    /* --------------------------------------------------------------------- */
    /*                                 RELASI                                */
    /* --------------------------------------------------------------------- */

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /* --------------------------------------------------------------------- */

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\OrderStatus::COMPLETED;
        $this->save();
    }
}
