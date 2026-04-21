<?php

namespace App\Http\Responses;

use Illuminate\Support\Collection;

class ExecuteMultiTransferResponse extends BaseResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct(Collection $transfers)
    {
        parent::__construct(201, 'Multi transfer executed successfully', $transfers);
    }
}
