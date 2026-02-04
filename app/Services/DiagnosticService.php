<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redis;
use App\Constants\AppConstant;

class DiagnosticService
{
    /**
     * Get real-time system performance from Redis.
     */
    public function getPerformanceMetrics(): array
    {
        try {
            $rawLogs = Redis::lrange(AppConstant::PERFORMANCE_LOG_KEY, 0, 9);
            return array_map(function($log) {
                $data = json_decode($log, true);
                $data['date'] = \Carbon\Carbon::parse($data['date'])->diffForHumans();
                return $data;
            }, $rawLogs);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get system-level hardware stats.
     */
    public function getHardwareStats(): array
    {
        return [
            'cpu_load' => sys_getloadavg(),
            'memory'   => $this->getMemoryInfo(),
            'db_size'  => $this->getDatabaseSize(),
        ];
    }

    /**
     * Get recent critical application logs.
     */
    public function getRecentErrors(int $limit = 5): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) return [];

        $lines = file($logPath);
        $errors = array_filter($lines, function($line) {
            return str_contains($line, 'ERROR') || str_contains($line, 'CRITICAL');
        });

        return array_slice($errors, -$limit);
    }

    private function getMemoryInfo(): array
    {
        if (!is_readable("/proc/meminfo")) {
            return ['total' => 'N/A', 'used_percent' => 0];
        }

        $data = explode("\n", file_get_contents("/proc/meminfo"));
        $memInfo = [];
        foreach ($data as $line) {
            if (empty($line)) continue;
            list($key, $val) = explode(":", $line);
            $memInfo[$key] = trim($val);
        }

        $total = (int) filter_var($memInfo['MemTotal'], FILTER_SANITIZE_NUMBER_INT) / 1024;
        $free = (int) filter_var($memInfo['MemFree'], FILTER_SANITIZE_NUMBER_INT) / 1024;
        
        return [
            'total' => round($total / 1024, 2) . ' GB',
            'used_percent' => round((($total - $free) / $total) * 100, 1)
        ];
    }

    private function getDatabaseSize(): string
    {
        $dbName = config('database.connections.mysql.database');
        $query = "SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?";
        $result = DB::select($query, [$dbName]);
        return round($result[0]->size ?? 0, 2) . ' MB';
    }
}
