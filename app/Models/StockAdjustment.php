<?php

namespace App\Models;

use App\Observers\StockAdjustmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

#[ObservedBy(StockAdjustmentObserver::class)]
class StockAdjustment extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        static::saving(function (StockAdjustment $sa) {
            if ($sa->quantity_adjusted < 1) {
                throw ValidationException::withMessages([
                    'quantity_adjusted' => 'Quantity Adjustment harus minimal 1. Tidak boleh negatif atau nol.',
                ]);
            }
        });
    }
}
