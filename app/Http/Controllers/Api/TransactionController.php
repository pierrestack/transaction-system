<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\InitDepositRequest;
use App\Http\Requests\InitWithdrawalRequest;
use App\Http\Requests\WithdrawalRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    protected TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function initDeposit(InitDepositRequest $request)
    {
        $data = $request->validated();

        $transfer = $this->service->deposit(
            $data['account_number'], 
            $data['amount'], 
            $data['description'] ?? ''
        );

        return response()->json([
            'token' => $transfer->token,
            'reference' => $transfer->reference,
        ]);
    }

    public function executeDeposit(DepositRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeDeposit($data['token']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
        return response()->json([
            'message' => 'Deposit executed successfully',
            'data' => $result
        ]);
    }

    public function initWithdrawal(InitWithdrawalRequest $request)
    {
        $data = $request->validated();

        $transfer = $this->service->withdrawal(
            $data['account_number'], 
            $data['amount'], 
            $data['description'] ?? ''
        );

        return response()->json([
            'token' => $transfer->token,
            'reference' => $transfer->reference,
        ]);
    }

    public function executeWithdrawal(WithdrawalRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeWithdrawal($data['token']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
        return response()->json([
            'message' => 'Withdrawal executed successfully',
            'data' => $result
        ]);
    }
}
