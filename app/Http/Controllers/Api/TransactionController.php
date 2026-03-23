<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitDepositRequest;
use App\Http\Requests\InitTransferRequest;
use App\Http\Requests\InitWithdrawalRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Responses\ErrorTransactionResponse;
use App\Http\Responses\ExecuteTransactionResponse;
use App\Http\Responses\InitTransactionResponse;
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
        try {
            $transfer = $this->service->deposit(
                $data['account_number'], 
                $data['amount'], 
                $data['description'] ?? ''
            );
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new InitTransactionResponse($transfer);
    }

    public function executeDeposit(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeDeposit($data['token']);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new ExecuteTransactionResponse($result);
    }

    public function initWithdrawal(InitWithdrawalRequest $request)
    {
        $data = $request->validated();
        try {
             $transfer = $this->service->withdrawal(
                $data['account_number'], 
                $data['amount'], 
                $data['description'] ?? ''
            );
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new InitTransactionResponse($transfer);
    }

    public function executeWithdrawal(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeWithdrawal($data['token']);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new ExecuteTransactionResponse($result);
    }

    public function initTransfer(InitTransferRequest $request)
    {
        $data = $request->validated();
        try {
             $transfer = $this->service->transfer(
                $data['from_account_number'], 
                $data['to_account_number'], 
                $data['amount'], 
                $data['description'] ?? ''
            );
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new InitTransactionResponse($transfer);
    }

    public function executeTransfer(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeTransfer($data['token']);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new ExecuteTransactionResponse($result);
    }
}
