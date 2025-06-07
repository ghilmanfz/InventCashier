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

    protected static function booted(): void
    {
        // Saat membuat order baru, assign user_id dan inisialisasi total = 0
        static::creating(function (self $order) {
            $order->user_id = auth()->id();
            $order->total   = 0;
        });

        // Saat menyimpan (baik create maupun update), hitung ulang total & profit
        static::saving(function (self $order) {
            // reload detail untuk jaga–jaga
            $order->loadMissing('orderDetails.product');

            // hitung subtotal (jumlah harga semua detail)
            $subtotal = $order->orderDetails->sum(fn($detail) => $detail->price * $detail->quantity);

            // total = subtotal - discount (jika ada)
            $order->attributes['total'] = $subtotal - ($order->discount ?? 0);

            // profit = Σ ((harga – cost_price) × qty)
            $profit = $order->orderDetails->reduce(function ($carry, $detail) {
                return $carry + ($detail->price - $detail->product->cost_price) * $detail->quantity;
            }, 0);
            $order->attributes['profit'] = $profit;
        });
    }


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

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\OrderStatus::COMPLETED;
        $this->save();
    }
}
