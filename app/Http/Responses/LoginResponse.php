<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class LoginResponse implements Responsable
{
    protected $token;
    protected $tokenType;

    public function __construct($token, $tokenType = 'Bearer')
    {
        $this->token = $token;
        $this->tokenType = $tokenType;
    }

    public function toResponse($request)
    {
        return response()->json([
            'access_token' => $this->token,
            'token_type' => $this->tokenType,
        ]);
    }

}
