<?php

namespace App\Observers;

use App\Models\PriceHistory;
use App\Models\Product;

class ProductObserver
{

    public function updating(Product $product): void
    {
        // kalau cost_price ATAU price berubah
        if ($product->isDirty(['cost_price', 'price'])) {
            PriceHistory::create([
                'product_id'          => $product->id,
                'user_id'             => auth()->id(),
                'old_purchase_price'  => $product->getOriginal('cost_price'),
                'old_selling_price'   => $product->getOriginal('price'),
                'new_purchase_price'  => $product->cost_price,
                'new_selling_price'   => $product->price,
            ]);
        }
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
