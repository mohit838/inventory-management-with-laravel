<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class SwaggerBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->getUser();
        $password = $request->getPassword();

        if ($user && $password) {
            // Find user by email
            $authenticatedUser = User::where('email', $user)->first();

            // Check if user exists, password is correct, and role is superadmin
            if ($authenticatedUser && 
                Hash::check($password, $authenticatedUser->password) && 
                $authenticatedUser->role === User::ROLE_SUPERADMIN) {
                return $next($request);
            }
        }

        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="API Documentation"',
        ]);
    }
}
