<?php

namespace App\Resolvers;

use App\Models\Transfer;
use App\Services\TransferProcessor\TransactionProcessor;
use Illuminate\Support\Collection;

class TransferProcessorResolver
{
    private array $processors;

    public function __construct(array $processors)
    {
        $this->processors = $processors;
    }

    public function resolveMonoTransfer(Transfer $transfer): TransactionProcessor
    {
        foreach ($this->processors as $processor) {
            if ($processor->supportsMonoTransfers($transfer)) {
                return $processor;
            }
        }

        throw new \Exception("No processor found");
    }

    public function resolveMultiTransfer(Collection $transfers): TransactionProcessor
    {
        foreach ($this->processors as $processor) {
            if ($processor->supportsMultiTransfers($transfers)) {
                return $processor;
            }
        }

        throw new \Exception("No processor found");
    }
}
