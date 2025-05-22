<?php

namespace Database\Factories;

use App\Models\StockAdjustment;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockAdjustmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StockAdjustment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $productId = \App\Models\Product::query()->inRandomOrder()->value('id');
        $quantityAdjusted = $this->faker->numberBetween(-50, 50);

        return [
            'product_id' => $productId,
            'quantity_adjusted' => $quantityAdjusted,
            'reason' => $this->faker->sentence,
        ];
    }

    public function configure(): StockAdjustmentFactory
    {
        return $this->afterCreating(function (StockAdjustment $adjustment) {
            $product = $adjustment->product;
            $product->stock_quantity += $adjustment->quantity_adjusted;
            $product->save();
        });
    }
}
