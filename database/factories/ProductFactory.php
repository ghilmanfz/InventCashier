<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'category_id' => rand(1, 10),
            'name' => str($this->faker->words(3, true))->title(),
            'sku' => $this->faker->unique()->bothify('SKU########'),
            'description' => $this->faker->paragraph(true),
            'stock_quantity' => 1000,
            'cost_price' => $costPrice = $this->faker->numberBetween(10000, 100000),
            'price' => $costPrice + ($costPrice * (20 / 100)),
            'is_tempered_glass' => $this->faker->boolean(20), // 20 % sample tempered glass
        ];
    }
}
