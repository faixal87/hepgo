<?php

namespace App\Services;

use App\Enums\VerificationStatus;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

class PropertyWorkflowService
{
    /**
     * @return array<string, mixed>
     */
    public function prepareFormData(Property $property): array
    {
        $owner = $property->owner;

        return [
            'owner_name' => $owner?->name,
            'owner_phone' => $owner?->phone,
            'owner_whatsapp_number' => $owner?->whatsapp_number,
            'owner_email' => $owner?->email,
            'facility_ids' => $property->facilities()->pluck('facilities.id')->map(fn ($id) => (string) $id)->all(),
            'new_uploaded_images' => [],
            'new_image_entries' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function preparePropertyPayload(array $data, ?Property $property = null): array
    {
        $owner = $this->syncOwner($data, $property);

        $data['owner_id'] = $owner->getKey();

        unset(
            $data['owner_name'],
            $data['owner_phone'],
            $data['owner_whatsapp_number'],
            $data['owner_email'],
            $data['facility_ids'],
            $data['new_uploaded_images'],
            $data['new_image_entries'],
        );

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<int, int>
     */
    public function extractFacilityIds(array &$data): array
    {
        $facilityIds = collect(Arr::wrap($data['facility_ids'] ?? []))
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        unset($data['facility_ids']);

        return $facilityIds;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{files: array<int, TemporaryUploadedFile|string>, entries: array<int, array<string, mixed>>}
     */
    public function extractUploadedImages(array &$data): array
    {
        $files = array_values(Arr::wrap($data['new_uploaded_images'] ?? []));
        $entries = array_values(Arr::wrap($data['new_image_entries'] ?? []));

        unset($data['new_uploaded_images'], $data['new_image_entries']);

        return [
            'files' => $files,
            'entries' => $entries,
        ];
    }

    /**
     * @param  array<int, int>  $facilityIds
     */
    public function syncFacilities(Property $property, array $facilityIds): void
    {
        $property->facilities()->sync($facilityIds);
    }

    /**
     * @param  array<int, TemporaryUploadedFile|string>  $files
     * @param  array<int, array<string, mixed>>  $entries
     */
    public function syncUploadedImages(Property $property, array $files, array $entries): void
    {
        if ($files === []) {
            return;
        }

        $imageService = app(PropertyImageService::class);
        $metadataByKey = collect($entries)->keyBy(fn (array $entry) => (string) ($entry['upload_key'] ?? ''));
        $hasExistingThumbnail = $property->images()->where('is_thumbnail', true)->exists();
        $thumbnailAssigned = false;

        foreach ($files as $index => $file) {
            if (! $file instanceof TemporaryUploadedFile) {
                continue;
            }

            if (! $this->temporaryUploadExists($file)) {
                continue;
            }

            $uploadKey = (string) $index;
            $entry = $metadataByKey->get($uploadKey, []);
            $isThumbnail = (bool) ($entry['is_thumbnail'] ?? false);

            if (! $hasExistingThumbnail && ! $thumbnailAssigned && ! collect($entries)->contains(fn (array $item) => (bool) ($item['is_thumbnail'] ?? false))) {
                $isThumbnail = true;
                $thumbnailAssigned = true;
            }

            $imageService->storePropertyImage($property, $file, [
                'caption' => $entry['caption'] ?? null,
                'is_thumbnail' => $isThumbnail,
                'sort_order' => (int) ($entry['sort_order'] ?? ($index + 1)),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function syncOwner(array $data, ?Property $property = null): Owner
    {
        $ownerData = [
            'name' => $this->cleanText($data['owner_name'] ?? null),
            'phone' => $this->normalizePhone($data['owner_phone'] ?? null),
            'whatsapp_number' => $this->normalizePhone($data['owner_whatsapp_number'] ?? null),
            'email' => filled($data['owner_email'] ?? null) ? mb_strtolower(trim((string) $data['owner_email'])) : null,
        ];

        $owner = $property?->owner;

        if (! $owner) {
            return Owner::query()->create([
                ...$ownerData,
                'verification_status' => VerificationStatus::PENDING,
                'created_by' => auth()->id(),
            ]);
        }

        if ($owner->properties()->whereKeyNot($property?->getKey())->exists() && $this->ownerDataChanged($owner, $ownerData)) {
            return Owner::query()->create([
                ...$ownerData,
                'verification_status' => VerificationStatus::PENDING,
                'created_by' => auth()->id(),
            ]);
        }

        $owner->fill($ownerData)->save();

        return $owner->refresh();
    }

    /**
     * @param  array<string, mixed>  $ownerData
     */
    private function ownerDataChanged(Owner $owner, array $ownerData): bool
    {
        return collect($ownerData)->contains(
            fn ($value, string $key) => (string) ($owner->{$key} ?? '') !== (string) ($value ?? '')
        );
    }

    private function normalizePhone(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return preg_replace('/[^\d+]/', '', trim($value)) ?: null;
    }

    private function temporaryUploadExists(TemporaryUploadedFile $file): bool
    {
        try {
            return $file->exists();
        } catch (Throwable) {
            return false;
        }
    }

    private function cleanText(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return trim(strip_tags($value));
    }
}
