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
        // Only enforce in production (optional, but good for local dev)
        // If you want it everywhere, remove the if check
        if (app()->environment('production') || config('app.debug') === false) {
            $user = $request->getUser();
            $password = $request->getPassword();

            if ($user && $password) {
                // Find superadmin
                $superAdmin = User::role('superadmin')->first();

                if ($superAdmin && $user === $superAdmin->email && Hash::check($password, $superAdmin->password)) {
                    return $next($request);
                }
            }

            return response('Unauthorized', 401, [
                'WWW-Authenticate' => 'Basic realm="API Documentation"',
            ]);
        }

        return $next($request);
    }
}
