<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Milon\Barcode\Facades\DNS1DFacade as DNS1D;
use App\Models\Retur;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];              //  ← biar barcode boleh di-isi mass-assignment
    protected $casts = [
    'is_tempered_glass' => 'boolean',
    ];
    
    /* ---------- ACCESSOR BARCODE SVG ---------- */
    public function getBarcodeSvgAttribute(): string
    {
        $code = $this->barcode ?: $this->sku;
        $svg  = DNS1D::getBarcodeSVG($code, 'C128', 1.5, 40);   // ← pakai \DNS1D

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(Retur::class);
    }
    
    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }

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
        public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public static function booted()
    {
        /* Set saat membuat BARU */
        static::creating(function (self $product) {
            if (blank($product->barcode)) {
                $product->barcode = $product->sku;
            }
        });

        /* Set saat SKU diganti */
        static::saving(function (self $product) {
            if ($product->isDirty('sku')) {
                $product->barcode = $product->sku;   // atau logika lain
            }
        });
    }


}
