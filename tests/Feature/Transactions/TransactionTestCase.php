<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

abstract class TransactionTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Currency $currency;

    protected Account $account;

    protected Account $systemAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->currency = Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
        ]);

        $this->account = Account::create([
            'account_number' => 'ACC-123-456',
            'balance' => 150.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $this->systemAccount = Account::create([
            'account_number' => 'SYS-001',
            'balance' => 0.15,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'system',
            'system_name' => 'withdrawal-fee',
        ]);

        Sanctum::actingAs($this->user, ['*']);
    }
}
