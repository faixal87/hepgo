<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use App\Filament\Resources\Properties\PropertyResource;
use App\Services\PropertyImageService;
use App\Services\SystemNotificationService;
use App\Services\PropertyWorkflowService;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateProperty extends CreateRecord
{
    protected static string $resource = PropertyResource::class;

    protected static bool $canCreateAnother = false;

    private array $facilityIds = [];

    /**
     * @var array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|string>
     */
    private array $uploadedImages = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    private array $uploadedImageEntries = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $workflow = app(PropertyWorkflowService::class);
        $this->facilityIds = $workflow->extractFacilityIds($data);

        $uploads = $workflow->extractUploadedImages($data);
        $this->uploadedImages = $uploads['files'];
        $this->uploadedImageEntries = $uploads['entries'];

        $data = $workflow->preparePropertyPayload($data);
        $data['created_by'] = auth()->id();
        $data['status'] = PropertyAvailabilityStatus::PENDING->value;
        $data['verification_status'] = VerificationStatus::PENDING->value;
        $data['verified_by'] = null;
        $data['verified_at'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        app(PropertyWorkflowService::class)->syncFacilities($this->getRecord(), $this->facilityIds);
        app(PropertyWorkflowService::class)->syncUploadedImages(
            $this->getRecord(),
            $this->uploadedImages,
            $this->uploadedImageEntries,
        );
        app(PropertyImageService::class)->generateWebpVersionsForProperty($this->getRecord());

        if (auth()->user()?->hasRole('staff_jabatan')) {
            app(SystemNotificationService::class)->notifyListingSubmittedForReview($this->getRecord());
        }
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        if (auth()->user()?->hasRole('staff_jabatan')) {
            return 'Rumah sewa berjaya dihantar untuk semakan HEP';
        }

        return 'Rumah sewa berjaya disimpan';
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Save');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Cancel');
    }
}
