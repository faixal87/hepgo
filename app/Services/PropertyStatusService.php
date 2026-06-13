<?php

namespace App\Services;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\VerificationStatus;
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

            return $property->refresh();
        });
    }

    public function updateVerification(Property $property, string $newVerificationStatus, ?string $remarks = null): Property
    {
        $status = VerificationStatus::from($newVerificationStatus);

        return DB::transaction(function () use ($property, $status, $remarks): Property {
            $oldStatus = $property->verification_status;

            if ($oldStatus === $status) {
                return $property;
            }

            $property->forceFill([
                'verification_status' => $status,
                'remarks' => $remarks ?? $property->remarks,
                'verified_by' => $status === VerificationStatus::PENDING ? null : auth()->id(),
                'verified_at' => $status === VerificationStatus::PENDING ? null : now(),
            ])->save();

            $property->statusLogs()->create([
                'old_verification_status' => $oldStatus,
                'new_verification_status' => $status,
                'changed_by' => auth()->id(),
                'remarks' => $remarks,
                'created_at' => now(),
            ]);

            return $property->refresh();
        });
    }
}
