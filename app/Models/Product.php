<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function costPrice(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str($value)->replace(',', '')
        );
    }

    public function price(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => str($value)->replace(',', '')
        );
    }
}
