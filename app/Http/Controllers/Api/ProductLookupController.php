<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductLookupController extends Controller
{
    public function __invoke(string $code): JsonResponse
    {
        $product = Product::query()
            ->where('barcode', $code)
            ->orWhere('sku', $code)
            ->firstOrFail();

        return response()->json([
            'name'           => $product->name,
            'price'          => (int) $product->price,
            'stock_quantity' => (int) $product->stock_quantity,
            'sku'            => $product->sku,
            'barcode'        => $product->barcode,
        ]);
    }
}
