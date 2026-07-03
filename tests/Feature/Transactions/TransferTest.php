<?php

namespace Tests\Feature\Transactions;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class TransferTest extends TransactionTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    #[Test]
    public function authenticated_user_can_initialize_a_mono_transfer()
    {
        $receiverAccount = Account::create([
            'account_number' => 'ACC-654-321',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $payload = [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => $receiverAccount->account_number,
            'amount' => 50.00,
            'description' => 'Wallet transfer',
        ];

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

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
            'receiver_account_id' => $receiverAccount->id,
            'amount' => '50.000000',
            'currency_id' => $this->currency->id,
            'type' => 'transfer',
            'status' => 'pending',
            'description' => 'Wallet transfer',
        ]);
    }

    public function test_authenticated_user_can_execute_a_mono_transfer_with_token()
    {
        $receiverAccount = Account::create([
            'account_number' => 'ACC-654-321',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $initResponse = $this->postJson('/api/transactions/init-transfer', [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => $receiverAccount->account_number,
            'amount' => 50.00,
            'description' => 'Wallet transfer',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse = $this->postJson('/api/transactions/execute-transfer', [
            'token' => $token,
        ]);

        $executeResponse->assertStatus(201)
            ->assertJsonFragment([
                'status_code' => 201,
                'message' => 'Transfer executed successfully',
            ])
            ->assertJsonPath('data.token', $token)
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('transfers', [
            'sender_account_id' => $this->account->id,
            'receiver_account_id' => $receiverAccount->id,
            'amount' => '50.000000',
            'currency_id' => $this->currency->id,
            'type' => 'transfer',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => '95.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $receiverAccount->id,
            'balance' => '50.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->systemAccount->id,
            'balance' => '5.150000',
        ]);
    }

    public function test_authenticated_user_can_initialize_a_multi_transfer()
    {
        $receiverAccount1 = Account::create([
            'account_number' => 'ACC-111-111',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $receiverAccount2 = Account::create([
            'account_number' => 'ACC-222-222',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $payload = [
            'transfers' => [
                [
                    'from_account_number' => $this->account->account_number,
                    'to_account_number' => $receiverAccount1->account_number,
                    'amount' => 30.00,
                    'description' => 'First payment',
                ],
                [
                    'from_account_number' => $this->account->account_number,
                    'to_account_number' => $receiverAccount2->account_number,
                    'amount' => 20.00,
                    'description' => 'Second payment',
                ],
            ],
        ];

        $response = $this->postJson('/api/transactions/init-multi-transfer', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status_code',
                'message',
                'data' => [
                    '*' => ['token', 'reference'],
                ],
            ]);

        $this->assertDatabaseHas('transfers', [
            'sender_account_id' => $this->account->id,
            'receiver_account_id' => $receiverAccount1->id,
            'amount' => '30.000000',
            'currency_id' => $this->currency->id,
            'type' => 'transfer',
            'status' => 'pending',
            'description' => 'First payment',
        ]);

        $this->assertDatabaseHas('transfers', [
            'sender_account_id' => $this->account->id,
            'receiver_account_id' => $receiverAccount2->id,
            'amount' => '20.000000',
            'currency_id' => $this->currency->id,
            'type' => 'transfer',
            'status' => 'pending',
            'description' => 'Second payment',
        ]);
    }

    public function test_authenticated_user_can_execute_a_multi_transfer_with_tokens()
    {
        $receiverAccount1 = Account::create([
            'account_number' => 'ACC-111-111',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $receiverAccount2 = Account::create([
            'account_number' => 'ACC-222-222',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $initResponse = $this->postJson('/api/transactions/init-multi-transfer', [
            'transfers' => [
                [
                    'from_account_number' => $this->account->account_number,
                    'to_account_number' => $receiverAccount1->account_number,
                    'amount' => 30.00,
                    'description' => 'First payment',
                ],
                [
                    'from_account_number' => $this->account->account_number,
                    'to_account_number' => $receiverAccount2->account_number,
                    'amount' => 20.00,
                    'description' => 'Second payment',
                ],
            ],
        ]);

        $initResponse->assertStatus(201);

        $tokens = array_column($initResponse->json('data'), 'token');

        $executeResponse = $this->postJson('/api/transactions/execute-multi-transfer', [
            'tokens' => $tokens,
        ]);

        $executeResponse->assertStatus(201)
            ->assertJsonFragment([
                'status_code' => 201,
                'message' => 'Multi transfer executed successfully',
            ])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.status', 'completed')
            ->assertJsonPath('data.1.status', 'completed');

        $this->assertDatabaseHas('transfers', [
            'token' => $tokens[0],
            'status' => 'completed',
            'amount' => '30.000000',
        ]);

        $this->assertDatabaseHas('transfers', [
            'token' => $tokens[1],
            'status' => 'completed',
            'amount' => '20.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => '95.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $receiverAccount1->id,
            'balance' => '30.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $receiverAccount2->id,
            'balance' => '20.000000',
        ]);

        $this->assertEqualsWithDelta(5.15, Account::find($this->systemAccount->id)->balance, 0.000001);
    }

    public function test_execute_multi_transfer_with_invalid_tokens_returns_error()
    {
        $response = $this->postJson('/api/transactions/execute-multi-transfer', [
            'tokens' => ['invalid-token-1', 'invalid-token-2'],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tokens.0', 'tokens.1']);
    }

    public function test_guest_cannot_initialize_a_mono_transfer()
    {
        $guard = Auth::guard('sanctum');
        $reflection = new \ReflectionObject($guard);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($guard, null);

        $payload = [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => 'ACC-654-321',
            'amount' => 50.00,
            'description' => 'Wallet transfer',
        ];

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

        $response->assertStatus(401);
    }

    public function test_cannot_execute_mono_transfer_twice_with_same_token()
    {
        $receiverAccount = Account::create([
            'account_number' => 'ACC-654-321',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $initResponse = $this->postJson('/api/transactions/init-transfer', [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => $receiverAccount->account_number,
            'amount' => 50.00,
            'description' => 'Wallet transfer',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse1 = $this->postJson('/api/transactions/execute-transfer', [
            'token' => $token,
        ]);

        $executeResponse1->assertStatus(201);

        $executeResponse2 = $this->postJson('/api/transactions/execute-transfer', [
            'token' => $token,
        ]);

        $executeResponse2->assertStatus(400);
    }

    public function test_transfer_amount_must_be_positive()
    {
        $payload = [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => 'ACC-654-321',
            'amount' => 0,
            'description' => 'Invalid transfer',
        ];

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');

        $payload['amount'] = -10.00;

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('amount');
    }

    public function test_sender_account_must_exist()
    {
        $payload = [
            'from_account_number' => 'NON-EXISTENT-FROM',
            'to_account_number' => 'ACC-654-321',
            'amount' => 10.00,
            'description' => 'Transfer',
        ];

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('from_account_number');
    }

    public function test_receiver_account_must_exist()
    {
        $payload = [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => 'NON-EXISTENT-TO',
            'amount' => 10.00,
            'description' => 'Transfer',
        ];

        $response = $this->postJson('/api/transactions/init-transfer', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('to_account_number');
    }

    public function test_execute_transfer_with_insufficient_balance()
    {
        $receiverAccount = Account::create([
            'account_number' => 'ACC-999-000',
            'balance' => 0.00,
            'currency_id' => $this->currency->id,
            'status' => 'active',
            'type' => 'user',
        ]);

        $initResponse = $this->postJson('/api/transactions/init-transfer', [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => $receiverAccount->account_number,
            'amount' => 100000.00,
            'description' => 'Large transfer',
        ]);

        $initResponse->assertStatus(400);

        $this->assertDatabaseMissing('transfers', [
            'sender_account_id' => $this->account->id,
            'amount' => '100000.000000',
        ]);
    }

    public function test_same_account_transfer_charges_fee()
    {
        $amount = 10.00;

        $initResponse = $this->postJson('/api/transactions/init-transfer', [
            'from_account_number' => $this->account->account_number,
            'to_account_number' => $this->account->account_number,
            'amount' => $amount,
            'description' => 'Self transfer',
        ]);

        $initResponse->assertStatus(201);

        $token = $initResponse->json('data.token');

        $executeResponse = $this->postJson('/api/transactions/execute-transfer', [
            'token' => $token,
        ]);

        $executeResponse->assertStatus(201)
            ->assertJsonPath('data.status', 'completed');

        $fee = $amount * 0.1;

        $this->assertDatabaseHas('accounts', [
            'id' => $this->account->id,
            'balance' => '149.000000',
        ]);

        $this->assertDatabaseHas('accounts', [
            'id' => $this->systemAccount->id,
            'balance' => number_format($this->systemAccount->balance + $fee, 6, '.', ''),
        ]);
    }
}
