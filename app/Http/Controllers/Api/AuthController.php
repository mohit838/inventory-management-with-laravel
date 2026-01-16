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
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Auth", description: "API Endpoints for Authentication")]
class AuthController extends Controller
{
    protected JwtService $jwt;

    public function __construct(JwtService $jwt)
    {
        $this->jwt = $jwt;
    }

    #[OA\Post(
        path: "/api/v1/register",
        tags: ["Auth"],
        summary: "Register a new user",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string", format: "password"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "User created")
        ]
    )]
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

    #[OA\Post(
        path: "/api/v1/login",
        tags: ["Auth"],
        summary: "User login",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string", format: "password")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Successful login")
        ]
    )]
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

    #[OA\Post(
        path: "/api/v1/refresh",
        tags: ["Auth"],
        summary: "Refresh access token",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["refresh_token"],
                properties: [
                    new OA\Property(property: "refresh_token", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Token refreshed")
        ]
    )]
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

    #[OA\Post(
        path: "/api/v1/logout",
        tags: ["Auth"],
        summary: "Logout user",
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "refresh_token", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Logged out")
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
