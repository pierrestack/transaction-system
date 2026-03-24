<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class BaseResponse implements Responsable
{
    protected $statusCode;
    protected $message;
    protected $data;

    public function __construct($statusCode = 200, $message = '', $data = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->data = $data;
    }

    public function toResponse($request)
    {
        return response()->json([
            'status_code' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->data,
        ], $this->statusCode);
    }
}
