<?php

namespace App\Http\Controllers\Api;

use App\DTO\LoginData;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $dto = \App\DTO\RegisterData::fromRequest($request);

        // Force role to user for public registration
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
            'role' => User::ROLE_USER,
            'active' => true,
        ]);

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $dto = LoginData::fromRequest($request);

        $v = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::where('email', $dto->email)->first();
        if (! $user || ! Hash::check($dto->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (! $user->active) {
            return response()->json(['message' => 'Account is inactive'], 403);
        }

        $access = $this->jwt->generateAccessToken($user->id, ['role' => $user->role]);
        $refresh = $this->jwt->createRefreshToken($user->id);

        return response()->json([
            'access_token' => $access,
            'token_type' => 'bearer',
            'expires_in' => 900,
            'refresh_token' => $refresh->plain_token,
            'user' => $user
        ]);
    }

    public function refresh(Request $request)
    {
        $v = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $hash = hash('sha256', $request->refresh_token);
        $rt = \App\Models\RefreshToken::where('token', $hash)->where('revoked', false)->first();
        if (! $rt || $rt->expires_at && $rt->expires_at->isPast()) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $access = $this->jwt->generateAccessToken($rt->user_id);

        return response()->json([
            'access_token' => $access,
            'token_type' => 'bearer',
            'expires_in' => 900,
        ]);
    }

    public function logout(Request $request)
    {
        $refresh = $request->input('refresh_token');
        if ($refresh) {
            $this->jwt->revokeRefreshTokenByPlain($refresh);
        }
        Auth::logout();
        return response()->json(['message' => 'Logged out']);
    }
}
