<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization', '');
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return response()->json(['message' => 'Unauthorized. Bearer token missing.'], 401);
        }

        $token = $matches[1];

        try {
            $secret = config('app.key');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        if (empty($decoded->sub)) {
            return response()->json(['message' => 'Invalid token payload'], 401);
        }

        $user = User::find($decoded->sub);
        if (! $user || ! $user->active) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        Auth::login($user);
        return $next($request);
    }
}
