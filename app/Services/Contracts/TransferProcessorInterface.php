<?php

namespace App\Services\Contracts;

use App\Models\Transfer;
use Illuminate\Support\Collection;

interface TransferProcessorInterface
{
    public function initProcessDeposit(array $data): Transfer;
    public function processDeposit(string $token): Transfer;
    public function initWithdrawal(array $data): Transfer;
    public function processWithdrawal(string $token): Transfer;
    public function initMonoTransfer(array $data): Transfer;
    public function initMultiTransfer(array $data): Collection;
}
