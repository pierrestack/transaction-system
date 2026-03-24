<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $currencies = [
            ['name' => 'Ariary', 'code' => 'MGA', 'symbol' => 'Ar', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Euro', 'code' => 'EUR', 'symbol' => '€', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'British Pound', 'code' => 'GBP', 'symbol' => '£', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'CHF', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => '$', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => '$', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => '¥', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Indian Rupee', 'code' => 'INR', 'symbol' => '₹', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($currencies as $currency) {
            Currency::insert($currency);
        }
    }
}
