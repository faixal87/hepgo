<?php

namespace App\Services;

use App\Enums\VerificationStatus;
use App\Models\Owner;
use Illuminate\Support\Facades\DB;

class OwnerVerificationService
{
    public function updateVerification(Owner $owner, string $newVerificationStatus, ?string $remarks = null): Owner
    {
        $status = VerificationStatus::from($newVerificationStatus);

        return DB::transaction(function () use ($owner, $status, $remarks): Owner {
            $owner->forceFill([
                'verification_status' => $status,
                'remarks' => $remarks ?? $owner->remarks,
                'verified_by' => $status === VerificationStatus::PENDING ? null : auth()->id(),
                'verified_at' => $status === VerificationStatus::PENDING ? null : now(),
            ])->save();

            return $owner->refresh();
        });
    }
}
