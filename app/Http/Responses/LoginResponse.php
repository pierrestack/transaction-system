<?php

namespace App\Http\Responses;

class LoginResponse extends BaseResponse
{
    public function __construct($token, $tokenType = 'Bearer')
    {
        parent::__construct(200, 'Login successful', [
            'access_token' => $token,
            'token_type' => $tokenType,
        ]);
    }
}