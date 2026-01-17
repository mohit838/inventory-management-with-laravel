<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth', description: 'API Endpoints for Authentication')]
class AuthController extends Controller
{
    protected JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    #[OA\Post(
        path: '/api/v1/register',
        tags: ['Auth'],
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created'),
        ]
    )]
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Force role to user for public registration
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_USER,
            'active' => true,
        ]);

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    #[OA\Post(
        path: '/api/v1/login',
        tags: ['Auth'],
        summary: 'User login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email'),
                    new OA\Property(property: 'password', type: 'string', format: 'password'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Successful login'),
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            \Illuminate\Support\Facades\Log::warning("Login failed for email: {$request->email}");
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (! $user->active) {
            \Illuminate\Support\Facades\Log::warning("Inactive user login attempt: {$request->email}");
            return response()->json(['message' => 'Account is inactive'], 403);
        }

        \Illuminate\Support\Facades\Log::info("User logged in: {$user->id}");

        $access = $this->jwt->generateAccessToken($user->id, ['role' => $user->role]);
        $refresh = $this->jwt->createRefreshToken($user->id);

        return response()->json([
            'access_token' => $access,
            'token_type' => 'bearer',
            'expires_in' => 900,
            'refresh_token' => $refresh->plain_token,
            'user' => new UserResource($user),
        ]);
    }

    #[OA\Post(
        path: '/api/v1/refresh',
        tags: ['Auth'],
        summary: 'Refresh access token',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['refresh_token'],
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Token refreshed'),
        ]
    )]
    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $hash = hash('sha256', $request->refresh_token);
        $rt = \App\Models\RefreshToken::where('token', $hash)->where('revoked', false)->first();
        if (! $rt || ($rt->expires_at && $rt->expires_at->isPast())) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }

        $access = $this->jwt->generateAccessToken($rt->user_id);

        return response()->json([
            'access_token' => $access,
            'token_type' => 'bearer',
            'expires_in' => 900,
        ]);
    }

    #[OA\Post(
        path: '/api/v1/logout',
        tags: ['Auth'],
        summary: 'Logout user',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'refresh_token', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Logged out'),
        ]
    )]
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
