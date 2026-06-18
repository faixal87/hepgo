<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\Properties\Pages\Concerns\HandlesPropertyUploadValidation;
use App\Filament\Resources\Properties\PropertyResource;
use App\Services\PropertyImageService;
use App\Services\PropertyWorkflowService;
use App\Services\SystemNotificationService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EditProperty extends EditRecord
{
    use HandlesPropertyUploadValidation;

    protected static string $resource = PropertyResource::class;

    private array $facilityIds = [];

    /**
     * @var array<int, TemporaryUploadedFile|string>
     */
    private array $uploadedImages = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $uploadedImageEntries = [];

    private bool $requiresHepReviewAfterStaffEdit = false;

    private ?PropertyAvailabilityStatus $oldAvailabilityStatus = null;

    private ?VerificationStatus $oldVerificationStatus = null;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return [
            ...$data,
            ...app(PropertyWorkflowService::class)->prepareFormData($this->getRecord()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $workflow = app(PropertyWorkflowService::class);
        $this->facilityIds = $workflow->extractFacilityIds($data);

        $uploads = $workflow->extractUploadedImages($data);
        $this->uploadedImages = $uploads['files'];
        $this->uploadedImageEntries = $uploads['entries'];

        $record = $this->getRecord();
        $payload = $workflow->preparePropertyPayload($data, $record);

        if (
            auth()->user()?->hasRole('staff_jabatan')
            && $record->created_by === auth()->id()
            && $record->verification_status === VerificationStatus::VERIFIED
        ) {
            $this->requiresHepReviewAfterStaffEdit = true;
            $this->oldAvailabilityStatus = $record->status;
            $this->oldVerificationStatus = $record->verification_status;

            $payload['status'] = PropertyAvailabilityStatus::PENDING->value;
            $payload['verification_status'] = VerificationStatus::PENDING->value;
            $payload['verified_by'] = null;
            $payload['verified_at'] = null;
        }

        return $payload;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...PropertyResource::statusActions(),
            DeleteAction::make()
                ->label('Delete'),
            RestoreAction::make()
                ->label('Restore'),
        ];
    }

    protected function afterSave(): void
    {
        app(PropertyWorkflowService::class)->syncFacilities($this->getRecord(), $this->facilityIds);
        app(PropertyWorkflowService::class)->syncUploadedImages(
            $this->getRecord(),
            $this->uploadedImages,
            $this->uploadedImageEntries,
        );
        app(PropertyImageService::class)->generateWebpVersionsForProperty($this->getRecord());

        if (! $this->requiresHepReviewAfterStaffEdit) {
            return;
        }

        $record = $this->getRecord()->refresh();

        $record->statusLogs()->create([
            'old_status' => $this->oldAvailabilityStatus,
            'new_status' => $record->status,
            'old_verification_status' => $this->oldVerificationStatus,
            'new_verification_status' => $record->verification_status,
            'changed_by' => auth()->id(),
            'remarks' => 'Listing dikemaskini oleh staff jabatan dan dihantar semula untuk pengesahan HEP.',
            'created_at' => now(),
        ]);

        app(SystemNotificationService::class)->notifyListingUpdatedForReview($record);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        if ($this->requiresHepReviewAfterStaffEdit) {
            return 'Rumah sewa berjaya dikemaskini dan dihantar semula untuk semakan HEP';
        }

        return 'Rumah sewa berjaya dikemaskini';
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->label('Save');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel');
    }
}
