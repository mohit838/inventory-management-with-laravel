<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Audit Logs', description: 'System Audit Logs')]
class AuditLogController extends Controller
{
    #[OA\Get(
        path: '/api/v1/audit-logs',
        tags: ['Audit Logs'],
        summary: 'Get system audit logs (Cached)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function index(Request $request)
    {
        // Cache the first page of results for 60 seconds.
        // For pagination beyond page 1, we could verify cache keys based on page number, 
        // but for now, we optimize the main landing view "page_1" as requested.
        $page = $request->input('page', 1);
        $cacheKey = "audit_logs_page_{$page}";

        $logs = Cache::remember($cacheKey, 60, function () {
            // Eager load user to avoid N+1
            return AuditLog::with('user:id,name,email')
                ->latest()
                ->paginate(20);
        });

        return response()->json($logs);
    }
}
