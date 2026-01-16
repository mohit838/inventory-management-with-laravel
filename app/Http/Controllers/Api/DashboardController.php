<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\AnalyticsServiceInterface;

class DashboardController extends Controller
{
    public function __construct(protected AnalyticsServiceInterface $analytics)
    {}

    public function summary(Request $request)
    {
        $canViewRevenue = $request->user()->can('dashboard.view_revenue');
        
        return response()->json([
            'data' => $this->analytics->getSummary($request->user()->id, $canViewRevenue)
        ]);
    }

    public function chart(Request $request)
    {
        if (! $request->user()->can('dashboard.view_revenue')) {
            return response()->json(['message' => 'Unauthorized. Revenue permission required.'], 403);
        }

        $period = $request->input('period', 'monthly');
        return response()->json([
            'data' => $this->analytics->getSalesChart($request->user()->id, $period)
        ]);
    }
}
