<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return new LoginResponse($token);
        }

        return new BaseResponse(401, 'Invalid credentials');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return new BaseResponse(200, 'Logged out successfully');
    }
}
