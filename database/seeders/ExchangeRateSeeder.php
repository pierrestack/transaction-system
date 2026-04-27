<?php

namespace Database\Seeders;

use App\Models\ExchangeRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExchangeRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $exchangeRates = [
                ['base_currency_id' => 1, 'target_currency_id' => 2, 'rate' => 4870, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 3, 'rate' => 4145, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 4, 'rate' => 5600, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 5, 'rate' => 26, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 6, 'rate' => 5300, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 7, 'rate' => 3040, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 8, 'rate' => 2970, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 9, 'rate' => 630, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 1, 'target_currency_id' => 10, 'rate' => 44, 'created_at' => now(), 'updated_at' => now()],

                ['base_currency_id' => 2, 'target_currency_id' => 1, 'rate' => 0.000204, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 3, 'rate' => 0.93, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 4, 'rate' => 1.17, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 5, 'rate' => 0.0062, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 6, 'rate' => 1.04, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 7, 'rate' => 0.68, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 8, 'rate' => 0.61, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 9, 'rate' => 0.13, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 2, 'target_currency_id' => 10, 'rate' => 0.011, 'created_at' => now(), 'updated_at' => now()],

                ['base_currency_id' => 3, 'target_currency_id' => 1, 'rate' => 0.00024, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 2, 'rate' => 1.08, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 4, 'rate' => 1.26, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 5, 'rate' => 0.0067, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 6, 'rate' => 1.12, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 7, 'rate' => 0.73, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 8, 'rate' => 0.66, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 9, 'rate' => 0.14, 'created_at' => now(), 'updated_at' => now()],
                ['base_currency_id' => 3, 'target_currency_id' => 10, 'rate' => 0.012, 'created_at' => now(), 'updated_at' => now()],

            ];

            foreach ($exchangeRates as $exchangeRate) {
                ExchangeRate::insert($exchangeRate);
            }
    }
}
