<?php

namespace App\Services;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\UserStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\Properties\PropertyResource;
use App\Filament\Resources\PropertyReports\PropertyReportResource;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class SystemNotificationService
{
    public function notifyNewReport(PropertyReport $report): void
    {
        $recipients = $this->hepRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $report->loadMissing('property');

        $propertyTitle = $report->property?->title;
        $body = filled($propertyTitle)
            ? "Aduan baharu diterima untuk listing {$propertyTitle}. Jenis aduan: {$report->report_type->label()}."
            : "Aduan umum baharu diterima. Jenis aduan: {$report->report_type->label()}.";

        Notification::make()
            ->title('Aduan baharu memerlukan semakan HEP')
            ->body($body)
            ->warning()
            ->actions([
                Action::make('buka_aduan')
                    ->label('Open Report')
                    ->button()
                    ->markAsRead()
                    ->url(PropertyReportResource::getUrl('edit', ['record' => $report], panel: 'admin')),
            ])
            ->sendToDatabase($recipients, isEventDispatched: true);
    }

    public function notifyListingSubmittedForReview(Property $property): void
    {
        $recipients = $this->hepRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $property->loadMissing(['createdBy', 'area']);

        $submittedBy = $property->createdBy?->name ?? 'pengguna sistem';
        $location = $property->area?->name ?? 'kawasan tidak dinyatakan';

        Notification::make()
            ->title('Listing rumah sewa baharu menunggu semakan')
            ->body("{$property->title} di {$location} telah dihantar oleh {$submittedBy} dan sedang menunggu pengesahan HEP.")
            ->info()
            ->actions([
                Action::make('buka_listing')
                    ->label('Open Listing')
                    ->button()
                    ->markAsRead()
                    ->url(PropertyResource::getUrl('edit', ['record' => $property], panel: 'admin')),
            ])
            ->sendToDatabase($recipients, isEventDispatched: true);
    }

    public function notifyCreatorVerificationUpdate(Property $property, VerificationStatus $status, ?string $remarks = null): void
    {
        $recipient = $this->departmentStaffRecipient($property);

        if (! $recipient) {
            return;
        }

        [$title, $body, $notification] = match ($status) {
            VerificationStatus::VERIFIED => [
                'Listing anda telah disahkan',
                "Listing {$property->title} telah disahkan oleh HEP dan kini boleh dipaparkan di portal.",
                Notification::make()->success(),
            ],
            VerificationStatus::REJECTED => [
                'Listing anda ditolak',
                "Listing {$property->title} ditolak semasa semakan HEP. Sila semak semula maklumat yang dihantar.",
                Notification::make()->danger(),
            ],
            default => [
                'Status semakan listing anda dikemaskini',
                "Status semakan untuk {$property->title} telah dikemaskini.",
                Notification::make()->info(),
            ],
        };

        if (filled($remarks)) {
            $body .= " Catatan: {$remarks}";
        }

        $notification
            ->title($title)
            ->body($body)
            ->actions([
                Action::make('buka_listing')
                    ->label('Open Listing')
                    ->button()
                    ->markAsRead()
                    ->url(PropertyResource::getUrl('edit', ['record' => $property], panel: 'admin')),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    public function notifyCreatorAvailabilityUpdate(Property $property, PropertyAvailabilityStatus $status, ?string $remarks = null): void
    {
        $recipient = $this->departmentStaffRecipient($property);

        if (! $recipient) {
            return;
        }

        $body = "Status listing {$property->title} telah dikemaskini kepada {$status->label()}.";

        if (filled($remarks)) {
            $body .= " Catatan: {$remarks}";
        }

        Notification::make()
            ->title('Status listing anda telah berubah')
            ->body($body)
            ->info()
            ->actions([
                Action::make('buka_listing')
                    ->label('Open Listing')
                    ->button()
                    ->markAsRead()
                    ->url(PropertyResource::getUrl('edit', ['record' => $property], panel: 'admin')),
            ])
            ->sendToDatabase($recipient, isEventDispatched: true);
    }

    /**
     * @return Collection<int, User>
     */
    private function hepRecipients(): Collection
    {
        return User::query()
            ->role(['super_admin', 'hep_admin', 'hep_staff'])
            ->where('status', UserStatus::ACTIVE->value)
            ->get();
    }

    private function departmentStaffRecipient(Property $property): ?User
    {
        $property->loadMissing('createdBy');

        $creator = $property->createdBy;

        if (! $creator) {
            return null;
        }

        if ($creator->status !== UserStatus::ACTIVE) {
            return null;
        }

        if (! $creator->hasRole('staff_jabatan')) {
            return null;
        }

        return $creator;
    }
}
