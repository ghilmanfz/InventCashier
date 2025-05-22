<?php

namespace App\Observers;

use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class StockAdjustmentObserver
{
    public function created(StockAdjustment $stockAdjustment): void
    {
        DB::transaction(function () use ($stockAdjustment) {
            $product = $stockAdjustment->product;
            $product->stock_quantity += $stockAdjustment->quantity_adjusted;
            $product->save();
        });
    }

    public function deleting(StockAdjustment $stockAdjustment): void
    {
        DB::transaction(function () use ($stockAdjustment) {
            $product = $stockAdjustment->product;
            $product->stock_quantity -= $stockAdjustment->quantity_adjusted;
            $product->save();
        });
    }
}
