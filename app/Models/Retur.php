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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function related(): BelongsTo|null
    {
        // Jika tipe retur 'customer'
        if ($this->type === 'customer') {
            return $this->belongsTo(Customer::class, 'related_id');
        }
        // Jika tipe retur 'supplier'
        if ($this->type === 'supplier') {
            return $this->belongsTo(Supplier::class, 'related_id');
        }
        // Jika type bukan dua‐duanya, kita kembalikan null
        return null;
    }

    protected static function booted()
    {
        static::creating(function (Retur $retur) {
            if ($retur->quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Quantity harus minimal 1.',
                ]);
            }
            if ($retur->type === 'customer') {
                if (! Customer::where('id', $retur->related_id)->exists()) {
                    throw ValidationException::withMessages([
                        'related_id' => 'Customer tidak valid.',
                    ]);
                }
            } elseif ($retur->type === 'supplier') {
                if (! Supplier::where('id', $retur->related_id)->exists()) {
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
