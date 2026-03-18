<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    protected TransactionService $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    public function deposit(DepositRequest $request)
    {
        $data = $request->validated();

        $result = $this->service->deposit(
            $data['account_number'],
            $data['amount'],
            $data['description']
        );

        return response()->json([
            'message' => 'Deposit successful',
            'data' => $result
        ]);
    }
}
