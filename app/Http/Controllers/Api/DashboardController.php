<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\AnalyticsServiceInterface;
use App\Http\Resources\DashboardSummaryResource;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Dashboard", description: "API Endpoints for Dashboard Analytics")]
class DashboardController extends Controller
{
    public function __construct(protected AnalyticsServiceInterface $analytics)
    {}

    #[OA\Get(
        path: "/api/v1/dashboard/summary",
        tags: ["Dashboard"],
        summary: "Get dashboard summary stats",
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function summary(Request $request)
    {
        $user = $request->user();
        $canViewRevenue = $user ? $user->can('dashboard.view_revenue') : false;
        
        $data = $this->analytics->getSummary($user ? $user->id : 0, $canViewRevenue);
        return new DashboardSummaryResource($data);
    }

    #[OA\Get(
        path: "/api/v1/dashboard/chart",
        tags: ["Dashboard"],
        summary: "Get sales chart data",
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "period", in: "query", schema: new OA\Schema(type: "string", enum: ["daily", "weekly", "monthly"], default: "monthly"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation"),
            new OA\Response(response: 403, description: "Unauthorized")
        ]
    )]
    public function chart(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->can('dashboard.view_revenue')) {
            return response()->json(['message' => 'Unauthorized. Revenue permission required.'], 403);
        }

        $period = $request->input('period', 'monthly');
        $data = $this->analytics->getSalesChart($user->id, $period);
        
        return response()->json(['data' => $data]);
    }
}
