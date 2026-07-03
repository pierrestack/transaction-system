<?php

namespace Tests\Feature\Transactions;

use Illuminate\Support\Facades\Auth;

class DepositTest extends TransactionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_authenticated_user_can_initialize_a_deposit()
    {
        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 150.00,
            'description' => 'Initial deposit',
        ];

        $response = $this->postJson('/api/transactions/init-deposit', $payload);

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
            'receiver_account_id' => $this->account->id,
            'amount' => '150.000000',
            'currency_id' => $this->currency->id,
            'type' => 'deposit',
            'status' => 'pending',
            'description' => 'Initial deposit',
        ]);
    }

    public function test_authenticated_user_can_execute_a_deposit_with_token()
    {
        $initResponse = $this->postJson('/api/transactions/init-deposit', [
            'account_number' => $this->account->account_number,
            'amount' => 150.00,
            'description' => 'Initial deposit',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse = $this->postJson('/api/transactions/execute-deposit', [
            'token' => $token,
        ]);

        $executeResponse->assertStatus(201)
            ->assertJsonFragment([
                'status_code' => 201,
                'message' => 'Deposit executed successfully',
            ])
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('transfers', [
            'receiver_account_id' => $this->account->id,
            'amount' => '150.000000',
            'currency_id' => $this->currency->id,
            'type' => 'deposit',
            'status' => 'completed',
        ]);
    }

    public function test_guest_cannot_initialize_a_deposit()
    {
        $guard = Auth::guard('sanctum');
        $reflection = new \ReflectionObject($guard);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($guard, null);

        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 150.00,
            'description' => 'Initial deposit',
        ];

        $response = $this->postJson('/api/transactions/init-deposit', $payload);

        $response->assertStatus(401);
    }

    public function test_deposit_amount_must_be_positive()
    {
        $payload = [
            'account_number' => $this->account->account_number,
            'amount' => 0,
            'description' => 'Initial deposit',
        ];

        $response = $this->postJson('/api/transactions/init-deposit', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $payload['amount'] = -50.00;

        $response = $this->postJson('/api/transactions/init-deposit', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_account_must_exist()
    {
        $payload = [
            'account_number' => 'NON-EXISTENT-ACCOUNT',
            'amount' => 150.00,
            'description' => 'Initial deposit',
        ];

        $response = $this->postJson('/api/transactions/init-deposit', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('account_number');
    }

    public function test_cannot_execute_deposit_twice_with_same_token()
    {
        $initResponse = $this->postJson('/api/transactions/init-deposit', [
            'account_number' => $this->account->account_number,
            'amount' => 150.00,
            'description' => 'Initial deposit',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse1 = $this->postJson('/api/transactions/execute-deposit', [
            'token' => $token,
        ]);

        $executeResponse1->assertStatus(201);

        $executeResponse2 = $this->postJson('/api/transactions/execute-deposit', [
            'token' => $token,
        ]);

        $executeResponse2->assertStatus(400);
    }
}
