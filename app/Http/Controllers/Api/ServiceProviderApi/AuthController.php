<?php

namespace App\Http\Controllers\Api\ServiceProviderApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\ServiceProviderUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::guard('sp')->attempt($request->only('email', 'password'))) {
            return response()->error('Invalid login details', 401);
        }
        $user = ServiceProviderUser::where('email', $request['email'])->firstOrFail();
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
        return response()->success(null, $data);

    }

    public function logout(Request $request)
    {
        $hashedToken = Str::replace('Bearer ', '', $request->header('authorization'));
        $token = PersonalAccessToken::findToken($hashedToken);
        $user = $token->tokenable;
        $delete = $user->tokens()->delete();
        if (!$delete) {
            return response()->error('Something went wrong!', 403);
        }
        return response()->success('', $delete);
    }
}
