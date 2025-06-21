<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Retur extends Model
{
    use HasFactory;

    protected $guarded = [];

    /* ───────── RELASI PASTI ───────── */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'related_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'related_id');
    }

    /* Accessor untuk nama pihak yang terkait */
    public function getRelatedNameAttribute(): string
    {
        return $this->type === 'customer'
            ? $this->customer?->name ?? '-'
            : $this->supplier?->name ?? '-';
    }

    /* ---------- VALIDASI ---------- */
    protected static function booted()
    {
        static::creating(function (Retur $retur) {
            if ($retur->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity harus minimal 1.',
                ]);
            }

            if ($retur->type === 'customer') {
                if (! Customer::whereKey($retur->related_id)->exists()) {
                    throw ValidationException::withMessages([
                        'related_id' => 'Customer tidak valid.',
                    ]);
                }
            } elseif ($retur->type === 'supplier') {
                if (! Supplier::whereKey($retur->related_id)->exists()) {
                    throw ValidationException::withMessages([
                        'related_id' => 'Supplier tidak valid.',
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'type' => 'Tipe retur harus "customer" atau "supplier".',
                ]);
            }
        });
    }
}
