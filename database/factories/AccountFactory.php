<?php

namespace Database\Factories;

use App\Enums\StatusAccount;
use App\Models\Account;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' => Str::uuid(),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'currency_id' => $this->faker->randomElement(Currency::pluck('id')->toArray()),
            'status' => $this->faker->randomElement([StatusAccount::ACTIVE, StatusAccount::SUSPENDED, StatusAccount::CLOSED]),
        ];
    }
}
