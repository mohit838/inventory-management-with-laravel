<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an action to the database and clear cache.
     */
    public function log(string $action, string $description, ?Model $model = null): AuditLog
    {
        try {
            $user = Auth::user(); // May be null if not authenticated or in job
            
            $log = AuditLog::create([
                'user_id' => $user?->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => Request::ip(),
            ]);

            // Clear the cache so the new log appears immediately
            Cache::forget('audit_logs_page_1');

            return $log;
        } catch (\Exception $e) {
            // Fallback: If audit logging fails, just log to file so we don't break the app flow
            Log::error("Failed to write audit log: " . $e->getMessage());
            // Return empty model to satisfy return type safely
            return new AuditLog();
        }
    }
}
