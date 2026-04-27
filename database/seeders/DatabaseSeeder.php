<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
            $this->call([
                UserSeeder::class,
                CurrencySeeder::class,
                AccountSeeder::class,
                ExchangeRate::class,
            ]);
    }
}
