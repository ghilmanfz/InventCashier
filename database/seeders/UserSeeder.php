<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ghilman Faza',
            'email' => 'ghilmanfz@relabify.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        User::factory(2)->create();
    }
}
