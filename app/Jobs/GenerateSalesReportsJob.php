<?php

namespace App\Jobs;

use App\Services\Admin\SalesReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSalesReportsJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [
        30,
        60,
        120,
    ];

    public function __construct()
    {
        $this->onQueue('orders.reports.sales');
    }

    public function handle(SalesReportService $salesReportService): void
    {
        $salesReportService->refreshRecentReports();
    }
}
