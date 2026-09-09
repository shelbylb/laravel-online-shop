<?php

namespace App\Services\Admin;

use App\Models\DailySalesReport;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;

class SalesReportService
{
    private const int REPORT_DAYS = 30;

    private const array SUCCESSFUL_ORDER_STATUSES = [
        Order::STATUS_ACCEPTED,
        Order::STATUS_PAID,
        Order::STATUS_SHIPPED,
        Order::STATUS_COMPLETED,
    ];

    public function refreshReportForDate(CarbonInterface $date): DailySalesReport
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $ordersQuery = Order::query()
            ->whereBetween('created_at', [
                $startOfDay,
                $endOfDay,
            ]);

        $ordersCount = (clone $ordersQuery)->count();

        $salesCount = (clone $ordersQuery)
            ->whereIn('status', self::SUCCESSFUL_ORDER_STATUSES)
            ->count();

        $revenue = (clone $ordersQuery)
            ->whereIn('status', self::SUCCESSFUL_ORDER_STATUSES)
            ->sum('total_amount');

        $canceledCount = (clone $ordersQuery)
            ->where('status', Order::STATUS_CANCELED)
            ->count();

        return DailySalesReport::query()->updateOrCreate(
            [
                'report_date' => $date->toDateString(),
            ],
            [
                'orders_count' => $ordersCount,
                'sales_count' => $salesCount,
                'revenue' => $revenue,
                'canceled_count' => $canceledCount,
                'calculated_at' => now(),
            ]
        );
    }

    public function refreshRecentReports(): void
    {
        $dateTo = now()->toImmutable()->endOfDay();
        $dateFrom = $dateTo
            ->subDays(self::REPORT_DAYS - 1)
            ->startOfDay();

        foreach (CarbonPeriod::create($dateFrom, $dateTo) as $date) {
            $this->refreshReportForDate($date);
        }
    }

    public function getReportForPeriod(array $filters = []): array
    {
        [$dateFrom, $dateTo] = $this->resolvePeriodDates($filters);

        $dailyReports = DailySalesReport::query()
            ->whereBetween('report_date', [
                $dateFrom->toDateString(),
                $dateTo->toDateString(),
            ])
            ->orderByDesc('report_date')
            ->get();

        return [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'ordersCount' => (int) $dailyReports->sum('orders_count'),
            'salesCount' => (int) $dailyReports->sum('sales_count'),
            'revenue' => (float) $dailyReports->sum('revenue'),
            'canceled_count' => (int) $dailyReports->sum('canceled_count'),
            'daily_reports' => $dailyReports,
            'calculated_at' => $dailyReports->max('calculated_at'),
        ];
    }

    private function resolvePeriodDates(array $filters): array
    {
        $dateFrom = Arr::get($filters, 'date_from');
        $dateTo = Arr::get($filters, 'date_to');

        if ($dateFrom || $dateTo) {
            $resolvedDateTo = $dateTo
                ? CarbonImmutable::parse($dateTo)->endOfDay()
                : now()->toImmutable()->endOfDay();

            $resolvedDateFrom = $dateFrom
                ? CarbonImmutable::parse($dateFrom)->startOfDay()
                : $resolvedDateTo->startOfDay();

            if ($resolvedDateFrom->greaterThan($resolvedDateTo)) {
                return [$resolvedDateTo->startOfDay(), $resolvedDateFrom->endOfDay()];
            }

            return [$resolvedDateFrom, $resolvedDateTo];
        }

        $periodDays = (int) Arr::get($filters, 'period', 7);
        $periodDays = in_array($periodDays, [1, 7, 30], true) ? $periodDays : 7;

        $resolvedDateTo = now()->toImmutable()->endOfDay();
        $resolvedDateFrom = $resolvedDateTo
            ->subDays($periodDays - 1)
            ->startOfDay();

        return [$resolvedDateFrom, $resolvedDateTo];
    }
}
