<?php

namespace App\Filament\Widgets;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\ReportStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Services\VisitorTrackingService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortalOverviewWidget extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected ?string $heading = 'Ringkasan Pengurusan Rumah Sewa';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $visitorStats = app(VisitorTrackingService::class)->publicStats();

        $pendingVerification = Property::query()
            ->where('verification_status', VerificationStatus::PENDING->value)
            ->count();

        return [
            Stat::make('Jumlah Rumah Sewa', Property::query()->count())
                ->description('Semua rekod rumah sewa')
                ->descriptionIcon(Heroicon::OutlinedHomeModern)
                ->color('info'),

            Stat::make(
                'Rumah Masih Kosong',
                Property::query()
                    ->where('status', PropertyAvailabilityStatus::AVAILABLE->value)
                    ->where('verification_status', VerificationStatus::VERIFIED->value)
                    ->count()
            )
                ->description('Boleh dipaparkan kepada pelajar')
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),

            Stat::make('Rumah Telah Penuh', Property::query()->where('status', PropertyAvailabilityStatus::FULL->value)->count())
                ->description('Tidak menerima penyewa baharu')
                ->descriptionIcon(Heroicon::OutlinedXCircle)
                ->color('danger'),

            Stat::make('Menunggu Pengesahan', $pendingVerification)
                ->description('Listing yang perlu semakan HEP')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pendingVerification > 0 ? 'warning' : 'gray'),

            Stat::make('Aduan Baharu', PropertyReport::query()->where('status', ReportStatus::NEW->value)->count())
                ->description('Aduan awam yang belum disemak')
                ->descriptionIcon(Heroicon::OutlinedFlag)
                ->color('warning'),

            Stat::make('Pelawat Hari Ini', number_format($visitorStats['today_unique']))
                ->description(number_format($visitorStats['today_page_views']).' paparan halaman')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('info'),

            Stat::make('Jumlah Pelawat', number_format($visitorStats['total_unique']))
                ->description('Pelawat unik keseluruhan')
                ->descriptionIcon(Heroicon::OutlinedEye)
                ->color('gray'),
        ];
    }
}
