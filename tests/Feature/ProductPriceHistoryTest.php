<?php

use App\Models\PriceHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs price changes', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create([
        'cost_price' => 1000,
        'price'      => 2000,
    ]);

    // simulasi akses Filament (PUT/patch)
    $this->actingAs($user)
        ->put(route('filament.app.resources.products.update', $product), [
            'name'           => $product->name,
            'sku'            => $product->sku,
            'category_id'    => $product->category_id,
            'supplier_id'    => $product->supplier_id,
            'stock_quantity' => $product->stock_quantity,
            'cost_price'     => 1500,
            'price'          => 2500,
        ]);

    expect(PriceHistory::count())->toBe(1)
        ->and(PriceHistory::first()->user_id)->toBe($user->id);
});
