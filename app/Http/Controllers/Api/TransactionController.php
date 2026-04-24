<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InitDepositRequest;
use App\Http\Requests\InitMultiTransferRequest;
use App\Http\Requests\InitTransferRequest;
use App\Http\Requests\InitWithdrawalRequest;
use App\Http\Requests\MultiTransferRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Responses\ErrorTransactionResponse;
use App\Http\Responses\ExecuteMultiTransferResponse;
use App\Http\Responses\ExecuteTransactionResponse;
use App\Http\Responses\InitTransactionResponse;
use App\Http\Responses\InitMultiTransferResponse;
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
            $transfer = $this->service->deposit($data);
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
             $transfer = $this->service->withdrawal($data);
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

    public function initMonoTransfer(InitTransferRequest $request)
    {
        $data = $request->validated();
        try {
             $transfer = $this->service->monoTransfer($data);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new InitTransactionResponse($transfer);
    }

    public function executeMonoTransfer(TransactionRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeTransfer($data['token']);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new ExecuteTransactionResponse($result);
    }

    public function initMultiTransfer(InitMultiTransferRequest $request)
    {
        $data = $request->validated();
        try {
             $transfers = $this->service->multiTransfer($data);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new InitMultiTransferResponse($transfers);
    }

    public function executeMultiTransfer(MultiTransferRequest $request)
    {
        $data = $request->validated();
        try {
            $result = $this->service->executeMultiTransfer($data['tokens']);
        } catch (\Exception $e) {
            return new ErrorTransactionResponse($e->getMessage());
        }
        return new ExecuteMultiTransferResponse($result);
    }
}
