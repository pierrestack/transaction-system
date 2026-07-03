<?php

namespace Tests\Feature\Transactions;

use Illuminate\Support\Facades\Auth;

class WithdrawalTest extends TransactionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_authenticated_user_can_initialize_a_withdrawal()
    {
        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 100.00,
            'description' => 'ATM withdrawal',
        ];

        $response = $this->postJson('/api/transactions/init-withdrawal', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status_code',
                'message',
                'data' => [
                    'token',
                    'reference',
                ],
            ]);

        $this->assertDatabaseHas('transfers', [
            'sender_account_id' => $this->account->id,
            'amount' => '100.000000',
            'currency_id' => $this->currency->id,
            'type' => 'withdrawal',
            'status' => 'pending',
            'description' => 'ATM withdrawal',
        ]);
    }

    public function test_authenticated_user_can_execute_a_withdrawal()
    {
        $initResponse = $this->postJson('/api/transactions/init-withdrawal', [
            'account_number' => $this->account->account_number,
            'amount' => 100.00,
            'description' => 'ATM withdrawal',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse = $this->postJson('/api/transactions/execute-withdrawal', [
            'token' => $token,
        ]);

        $executeResponse->assertStatus(201)
            ->assertJsonFragment([
                'status_code' => 201,
                'message' => 'Withdrawal executed successfully',
            ])
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('transfers', [
            'sender_account_id' => $this->account->id,
            'amount' => '100.000000',
            'currency_id' => $this->currency->id,
            'type' => 'withdrawal',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => '50.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->systemAccount->id,
            'balance' => '0.150000',
        ]);
    }

    public function test_guest_cannot_initialize_a_withdrawal()
    {
        $guard = Auth::guard('sanctum');
        $reflection = new \ReflectionObject($guard);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($guard, null);

        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 100.00,
            'description' => 'ATM withdrawal',
        ];

        $response = $this->postJson('/api/transactions/init-withdrawal', $payload);

        $response->assertStatus(401);
    }

    public function test_withdrawal_amount_must_be_positive()
    {
        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 0,
            'description' => 'ATM withdrawal',
        ];

        $response = $this->postJson('/api/transactions/init-withdrawal', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $payload['amount'] = -25.00;

        $response = $this->postJson('/api/transactions/init-withdrawal', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_withdrawal_account_must_exist()
    {
        $payload = [
            'account_number' => 'NON-EXISTENT-ACCOUNT',
            'amount' => 100.00,
            'description' => 'ATM withdrawal',
        ];

        $response = $this->postJson('/api/transactions/init-withdrawal', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('account_number');
    }

    public function test_cannot_execute_withdrawal_twice_with_same_token()
    {
        $initResponse = $this->postJson('/api/transactions/init-withdrawal', [
            'account_number' => $this->account->account_number,
            'amount' => 100.00,
            'description' => 'ATM withdrawal',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse1 = $this->postJson('/api/transactions/execute-withdrawal', [
            'token' => $token,
        ]);

        $executeResponse1->assertStatus(201);

        $executeResponse2 = $this->postJson('/api/transactions/execute-withdrawal', [
            'token' => $token,
        ]);

        $executeResponse2->assertStatus(400);
    }
}
