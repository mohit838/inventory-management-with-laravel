<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Admin Logs', description: 'API Endpoints for viewing application logs (Superadmin only)')]
class LogViewerController extends Controller
{
    public function __construct(protected LogService $logService) {}

    #[OA\Get(
        path: '/api/v1/admin/logs',
        tags: ['Admin Logs'],
        summary: 'Get application logs (Superadmin only)',
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'lines', in: 'query', schema: new OA\Schema(type: 'integer', default: 100)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Return log lines'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $lines = $request->get('lines', 100);
        $logs = $this->logService->getLogs((int) $lines);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    #[OA\Delete(
        path: '/api/v1/admin/logs',
        tags: ['Admin Logs'],
        summary: 'Clear application logs (Superadmin only)',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logs cleared successfully'),
            new OA\Response(response: 403, description: 'Forbidden'),
        ]
    )]
    public function destroy(): JsonResponse
    {
        $this->logService->clearLogs();

        return response()->json([
            'success' => true,
            'message' => 'Application logs have been cleared successfully.',
        ]);
    }
}
