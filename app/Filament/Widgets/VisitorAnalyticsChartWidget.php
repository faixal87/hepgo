<?php

namespace App\Filament\Widgets;

use App\Services\VisitorTrackingService;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;

class VisitorAnalyticsChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Analitik Pelawat Portal';

    protected ?string $description = 'Pelawat unik dan paparan halaman mengikut bulan bagi tahun semasa.';

    protected ?string $maxHeight = '180px';

    protected string $color = 'info';

    protected function getData(): array
    {
        $stats = app(VisitorTrackingService::class)->currentYearMonthlyStats();

        return [
            'datasets' => [
                [
                    'label' => 'Pelawat Unik',
                    'data' => $stats['unique_visitors'],
                    'backgroundColor' => '#1d4ed8',
                    'borderColor' => '#1d4ed8',
                ],
                [
                    'label' => 'Paparan Halaman',
                    'data' => $stats['page_views'],
                    'backgroundColor' => '#ea580c',
                    'borderColor' => '#ea580c',
                ],
            ],
            'labels' => [
                'Jan',
                'Feb',
                'Mac',
                'Apr',
                'Mei',
                'Jun',
                'Jul',
                'Ogos',
                'Sep',
                'Okt',
                'Nov',
                'Dis',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'boxWidth' => 10,
                        'boxHeight' => 10,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'maxTicksLimit' => 5,
                    ],
                    'grid' => [
                        'color' => 'rgba(148, 163, 184, 0.18)',
                    ],
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
        ];
    }
}
