<?php

namespace App\Services;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
use App\Models\Owner;
use App\Models\Property;
use Illuminate\Support\Facades\DB;

class PropertyStatusService
{
    public function updateAvailability(Property $property, string $newStatus, ?string $remarks = null): Property
    {
        $status = PropertyAvailabilityStatus::from($newStatus);

        return DB::transaction(function () use ($property, $status, $remarks): Property {
            $oldStatus = $property->status;

            if ($oldStatus === $status) {
                return $property;
            }

            $property->forceFill([
                'status' => $status,
                'remarks' => $remarks ?? $property->remarks,
            ])->save();

            $property->statusLogs()->create([
                'old_status' => $oldStatus,
                'new_status' => $status,
                'changed_by' => auth()->id(),
                'remarks' => $remarks,
                'created_at' => now(),
            ]);

            app(SystemNotificationService::class)->notifyCreatorAvailabilityUpdate($property, $status, $remarks);

            return $property->refresh();
        });
    }

    public function updateVerification(Property $property, string $newVerificationStatus, ?string $remarks = null): Property
    {
        $status = VerificationStatus::from($newVerificationStatus);

        return DB::transaction(function () use ($property, $status, $remarks): Property {
            $oldVerificationStatus = $property->verification_status;
            $oldAvailabilityStatus = $property->status;

            if ($oldVerificationStatus === $status) {
                return $property;
            }

            $newAvailabilityStatus = $property->status;

            if (
                $status === VerificationStatus::VERIFIED
                && $property->status === PropertyAvailabilityStatus::PENDING
            ) {
                $newAvailabilityStatus = PropertyAvailabilityStatus::AVAILABLE;
            }

            $property->forceFill([
                'verification_status' => $status,
                'status' => $newAvailabilityStatus,
                'remarks' => $remarks ?? $property->remarks,
                'verified_by' => $status === VerificationStatus::PENDING ? null : auth()->id(),
                'verified_at' => $status === VerificationStatus::PENDING ? null : now(),
            ])->save();

            $this->syncOwnerVerification($property->owner, $status, $remarks);

            $property->statusLogs()->create([
                'old_status' => $oldAvailabilityStatus === $newAvailabilityStatus ? null : $oldAvailabilityStatus,
                'new_status' => $oldAvailabilityStatus === $newAvailabilityStatus ? null : $newAvailabilityStatus,
                'old_verification_status' => $oldVerificationStatus,
                'new_verification_status' => $status,
                'changed_by' => auth()->id(),
                'remarks' => $remarks,
                'created_at' => now(),
            ]);

            app(SystemNotificationService::class)->notifyCreatorVerificationUpdate($property, $status, $remarks);

            return $property->refresh();
        });
    }

    private function syncOwnerVerification(?Owner $owner, VerificationStatus $status, ?string $remarks = null): void
    {
        if (! $owner) {
            return;
        }

        $owner->forceFill([
            'verification_status' => $status,
            'remarks' => $remarks ?? $owner->remarks,
            'verified_by' => $status === VerificationStatus::PENDING ? null : auth()->id(),
            'verified_at' => $status === VerificationStatus::PENDING ? null : now(),
        ])->save();
    }
}
