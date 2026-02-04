<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use App\Constants\AppConstant;

class SystemControlController extends Controller
{
    public function __construct(protected \App\Services\DiagnosticService $diagnosticService)
    {
    }

    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize(\App\Constants\AppConstant::PERM_VIEW_DIAGNOSTICS);

        $hardwareStats = $this->diagnosticService->getHardwareStats();
        $cpuLoad       = $hardwareStats['cpu_load'];
        $memInfo       = $hardwareStats['memory'];
        $dbSize        = $hardwareStats['db_size'];
        
        $slowEndpoints = $this->diagnosticService->getPerformanceMetrics();
        $errorLogs     = $this->diagnosticService->getRecentErrors(5);

        return view('superadmin.system_health', compact('cpuLoad', 'memInfo', 'dbSize', 'errorLogs', 'slowEndpoints'));
    }
}
