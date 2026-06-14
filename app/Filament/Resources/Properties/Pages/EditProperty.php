<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\Pages\Concerns\HandlesPropertyUploadValidation;
use App\Filament\Resources\Properties\PropertyResource;
use App\Services\PropertyImageService;
use App\Services\PropertyWorkflowService;
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

        return $workflow->preparePropertyPayload($data, $this->getRecord());
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
    }

    protected function getSavedNotificationTitle(): ?string
    {
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
