<?php

namespace App\Models;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'property_id',
    'reporter_name',
    'reporter_phone',
    'reporter_email',
    'report_type',
    'message',
    'status',
    'handled_by',
    'handled_at',
    'admin_remarks',
])]
class PropertyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'report_type' => ReportType::class,
            'status' => ReportStatus::class,
            'handled_at' => 'datetime',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function markAs(ReportStatus $status, ?string $remarks = null): self
    {
        $this->forceFill([
            'status' => $status,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'admin_remarks' => $remarks ?? $this->admin_remarks,
        ])->save();

        return $this->refresh();
    }
}
