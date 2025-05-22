<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    public function definition(): array
    {
        Order::unsetEventDispatcher();

        return [
            'user_id' => 1,
            'customer_id' => rand(1, 50),
            'order_number' => $this->faker->unique()->bothify('ORD########'),
            'order_name' => ucfirst($this->faker->word),
            'discount' => $this->faker->numberBetween(5000, 10000),
            'total' => 0,
            'payment_method' => collect(\App\Enums\PaymentMethod::cases())->random(),
            'status' => collect(\App\Enums\OrderStatus::cases())->random(),
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function (Order $order) {
        })->afterCreating(function (Order $order) {
            $productIds = Product::query()->inRandomOrder()->take(rand(1, 5))->pluck('id');
            $orderDetails = $productIds->map(function ($productId) use ($order) {
                $quantity = rand(1, 10);
                $price = Product::find($productId)->price;
                $subtotal = $quantity * $price;

                return [
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            });

            OrderDetail::insert($orderDetails->toArray());

            $total = $orderDetails->sum('subtotal') - $order->discount;
            $order->total = $total;
            $order->profit = $total * 0.1;
            $order->save();
        });
    }
}
