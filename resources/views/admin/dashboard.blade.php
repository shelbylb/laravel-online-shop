@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    <style>
        .dashboard-section {
            margin-top: 20px;
        }

        .dashboard-periods {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .dashboard-filter-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(180px, 1fr)) auto;
            gap: 12px;
            align-items: end;
            margin-bottom: 20px;
        }

        .dashboard-form-actions {
            display: flex;
            gap: 8px;
        }

        .sales-report-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .sales-report-item {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
        }

        .sales-report-label {
            color: #6b7280;
            font-size: 14px;
        }

        .sales-report-value {
            font-size: 26px;
        }

        .sales-report-table {
            margin-top: 24px;
        }

        .sales-report-table h3 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        @media (max-width: 1200px) {
            .sales-report-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-filter-grid,
            .sales-report-grid {
                grid-template-columns: 1fr;
            }

            .dashboard-periods,
            .dashboard-form-actions {
                justify-content: flex-start;
            }
        }
    </style>

    <h1 class="page-title">Dashboard</h1>

    <div class="cards-grid">
        <div class="card">
            <div>Всего пользователей</div>
            <div class="stat-value">{{ $data->usersCount }}</div>
        </div>

        <div class="card">
            <div>Администраторы</div>
            <div class="stat-value">{{ $data->adminsCount }}</div>
        </div>

        <div class="card">
            <div>Менеджеры</div>
            <div class="stat-value">{{ $data->managersCount }}</div>
        </div>
    </div>

    @php
        $activePeriod = request('period', '7');
        $periodDays = in_array($activePeriod, ['1', '7', '30'], true) ? (int) $activePeriod : 7;
        $defaultDateTo = now()->format('Y-m-d');
        $defaultDateFrom = now()->subDays($periodDays - 1)->format('Y-m-d');
        $dateFrom = request('date_from', $defaultDateFrom);
        $dateTo = request('date_to', $defaultDateTo);
        $hasManualDates = request()->filled('date_from') || request()->filled('date_to');
    @endphp

    <div class="card dashboard-section">
        <div class="page-actions" style="align-items: flex-start;">
            <div>
                <h2 style="margin: 0 0 8px; font-size: 22px;">Отчет о продажах</h2>
                <div style="color: #6b7280; font-size: 14px;">
                    По умолчанию показаны продажи за последние 7 дней.
                </div>
            </div>

            <div class="dashboard-periods">
                <a href="{{ route('admin.dashboard', ['period' => 30]) }}" class="btn {{ $activePeriod === '30' && !$hasManualDates ? 'btn-primary' : 'btn-secondary' }}">30 дней</a>
                <a href="{{ route('admin.dashboard', ['period' => 7]) }}" class="btn {{ $activePeriod === '7' && !$hasManualDates ? 'btn-primary' : 'btn-secondary' }}">7 дней</a>
                <a href="{{ route('admin.dashboard', ['period' => 1]) }}" class="btn {{ $activePeriod === '1' && !$hasManualDates ? 'btn-primary' : 'btn-secondary' }}">1 день</a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.dashboard') }}">
            <div class="dashboard-filter-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="date_from" class="form-label">Дата с</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="date_to" class="form-label">Дата по</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                </div>

                <div class="dashboard-form-actions">
                    <button type="submit" class="btn btn-primary">Показать</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Сбросить</a>
                </div>
            </div>
        </form>

        <div class="sales-report-grid">
            <div class="sales-report-item">
                <div class="sales-report-label">Всего заказов</div>
                <div class="stat-value sales-report-value">{{ $report['ordersCount'] ?? 0 }}</div>
            </div>

            <div class="sales-report-item">
                <div class="sales-report-label">Продаж</div>
                <div class="stat-value sales-report-value">{{ $report['salesCount'] ?? 0 }}</div>
            </div>

            <div class="sales-report-item">
                <div class="sales-report-label">Выручка</div>
                <div class="stat-value sales-report-value">{{ number_format($report['revenue'] ?? 0, 2, ',', ' ') }}</div>
            </div>

            <div class="sales-report-item">
                <div class="sales-report-label">Отменено</div>
                <div class="stat-value sales-report-value">{{ $report['canceled_count'] ?? 0 }}</div>
            </div>
        </div>

        <div class="sales-report-table">
            <h3>Продажи по дням</h3>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Дата</th>
                            <th>Заказов</th>
                            <th>Продаж</th>
                            <th>Выручка</th>
                            <th>Отменено</th>
                            <th>Пересчитано</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($report['daily_reports'] ?? [] as $dailyReport)
                            <tr>
                                <td>{{ $dailyReport->report_date->format('d.m.Y') }}</td>
                                <td>{{ $dailyReport->orders_count }}</td>
                                <td>{{ $dailyReport->sales_count }}</td>
                                <td>
                                    {{ number_format(
                                        (float) $dailyReport->revenue,
                                        2,
                                        ',',
                                        ' '
                                    ) }} ₽
                                </td>
                                <td>{{ $dailyReport->canceled_count }}</td>
                                <td>{{ $dailyReport->calculated_at?->format('d.m.Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: #6b7280;">
                                    За выбранный период отчёты не найдены.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
