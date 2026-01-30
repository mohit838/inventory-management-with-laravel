<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SqlAnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected SqlAnalyticsService $analyticsService;

    public function __construct(SqlAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get analytics data
        $summary = $this->analyticsService->getSummary($user->id);
        $chartData = $this->analyticsService->getSalesChart($user->id, 'monthly');

        return view('dashboard.index', compact('user', 'summary', 'chartData'));
    }
}
