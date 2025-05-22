<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
        ]);

        Customer::factory(50)->create();
        Product::factory(50)->create();
        StockAdjustment::factory(50)->create();

        $this->call([OrderSeeder::class]);

        \Laravel\Prompts\info('Seeding completed!');
    }
}
