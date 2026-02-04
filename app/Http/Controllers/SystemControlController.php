<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;

class SystemControlController extends Controller
{
    /**
     * Display the low-level system diagnostic dashboard.
     */
    public function index()
    {
        Gate::authorize('view_diagnostics');

        // 1. Hardware Stats (Simulated for non-linux or via sys calls)
        $cpuLoad = sys_getloadavg();
        $memInfo = $this->getSystemMemInfo();

        // 2. Database Stats
        $dbSize = $this->getDatabaseSize();

        // 3. Last 5 Error Logs
        $errorLogs = $this->getRecentLogs(5);

        // 4. Slow Request Profiling (Captured from a separate storage or log)
        // For MVP, we'll parse logs for "request_duration" if we had them or use dummy data
        $slowEndpoints = [
            ['method' => 'GET', 'uri' => '/users', 'duration' => '1.2s', 'date' => now()->subMinutes(5)->diffForHumans()],
            ['method' => 'POST', 'uri' => '/settings/permissions', 'duration' => '0.8s', 'date' => now()->subMinutes(12)->diffForHumans()],
            ['method' => 'GET', 'uri' => '/infrastructure', 'duration' => '2.1s', 'date' => now()->subMinutes(45)->diffForHumans()],
        ];

        return view('superadmin.system_health', compact('cpuLoad', 'memInfo', 'dbSize', 'errorLogs', 'slowEndpoints'));
    }

    private function getSystemMemInfo()
    {
        if (!is_readable("/proc/meminfo")) {
            return ['total' => 'N/A', 'free' => 'N/A', 'used_percent' => 0];
        }

        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $memInfo = [];
        foreach ($data as $line) {
            if (empty($line)) continue;
            list($key, $val) = explode(":", $line);
            $memInfo[$key] = trim($val);
        }

        $total = (int) filter_var($memInfo['MemTotal'], FILTER_SANITIZE_NUMBER_INT) / 1024; // MB
        $free = (int) filter_var($memInfo['MemFree'], FILTER_SANITIZE_NUMBER_INT) / 1024; // MB
        
        return [
            'total' => round($total / 1024, 2) . ' GB',
            'free' => round($free / 1024, 2) . ' GB',
            'used_percent' => round((($total - $free) / $total) * 100, 1)
        ];
    }

    private function getDatabaseSize()
    {
        $dbName = config('database.connections.mysql.database');
        $query = "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?";
        $result = DB::select($query, [$dbName]);
        return round($result[0]->size ?? 0, 2) . ' MB';
    }

    private function getRecentLogs($limit = 5)
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) return [];

        $lines = file($logPath);
        $errors = array_filter($lines, function($line) {
            return str_contains($line, 'ERROR') || str_contains($line, 'CRITICAL');
        });

        return array_slice($errors, -$limit);
    }
}
