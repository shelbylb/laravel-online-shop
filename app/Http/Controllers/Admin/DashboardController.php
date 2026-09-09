<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use App\Services\Admin\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly SalesReportService $salesReportService
    ) {
    }

    public function index(Request $request): View
    {
        $data = $this->dashboardService->getDashboardData();
        $report = $this->salesReportService->getReportForPeriod($request->only([
            'period',
            'date_from',
            'date_to',
        ]));

        return view('admin.dashboard', compact('data', 'report'));
    }
}
