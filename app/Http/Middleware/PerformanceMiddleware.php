<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Constants\AppConstant;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000; // ms

        // Log if it exceeds threshold (and it's a GET/POST UI request, not diagnostic itself or landing)
        if ($duration > AppConstant::SLOW_THRESHOLD_MS && 
            !$request->routeIs('system.health') && 
            !$request->routeIs('landing')) {
            $this->logSlowRequest($request, $duration);
        }

        return $response;
    }

    private function logSlowRequest(Request $request, float $duration)
    {
        $log = [
            'method'   => $request->method(),
            'uri'      => $request->getRequestUri(),
            'duration' => round($duration / 1000, 2) . 's',
            'date'     => now()->toDateTimeString(),
            'timestamp'=> time()
        ];

        try {
            // Store last 10 slow requests in Redis list
            Redis::lpush(AppConstant::PERFORMANCE_LOG_KEY, json_encode($log));
            Redis::ltrim(AppConstant::PERFORMANCE_LOG_KEY, 0, 9);
        } catch (\Exception $e) {
            // Fallback to simple file logging or skip if Redis is not yet available
            Log::warning("Slow Request: " . $log['uri'] . " took " . $log['duration']);
        }
    }
}
