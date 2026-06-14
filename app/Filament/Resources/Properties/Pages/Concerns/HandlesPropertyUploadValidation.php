<?php

namespace App\Filament\Resources\Properties\Pages\Concerns;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Arr;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Throwable;

trait HandlesPropertyUploadValidation
{
    protected function beforeValidate(): void
    {
        $this->pruneMissingTemporaryPropertyUploads();
    }

    private function pruneMissingTemporaryPropertyUploads(): void
    {
        $files = Arr::wrap($this->data['new_uploaded_images'] ?? []);

        if ($files === []) {
            return;
        }

        $keyMap = [];
        $validFiles = [];
        $hasMissingUpload = false;

        foreach ($files as $oldKey => $file) {
            if ($file instanceof TemporaryUploadedFile && ! $this->temporaryPropertyUploadExists($file)) {
                $hasMissingUpload = true;

                continue;
            }

            $newKey = (string) count($validFiles);
            $keyMap[(string) $oldKey] = $newKey;
            $validFiles[] = $file;
        }

        if (! $hasMissingUpload) {
            return;
        }

        $this->data['new_uploaded_images'] = $validFiles;
        $this->data['new_image_entries'] = collect(Arr::wrap($this->data['new_image_entries'] ?? []))
            ->filter(fn (array $entry): bool => array_key_exists((string) ($entry['upload_key'] ?? ''), $keyMap))
            ->map(function (array $entry) use ($keyMap): array {
                $entry['upload_key'] = $keyMap[(string) ($entry['upload_key'] ?? '')];

                return $entry;
            })
            ->values()
            ->all();

        Notification::make()
            ->title('Gambar perlu dipilih semula')
            ->body('Satu atau lebih gambar yang dipilih sudah tamat tempoh atau tidak lengkap. Sila pilih semula gambar tersebut sebelum tekan Save.')
            ->danger()
            ->send();

        throw new Halt;
    }

    private function temporaryPropertyUploadExists(TemporaryUploadedFile $file): bool
    {
        try {
            return $file->exists();
        } catch (Throwable) {
            return false;
        }
    }
}
