<?php

namespace App\Enums;

enum ReportStatus: string
{
    case NEW = 'new';
    case REVIEWING = 'reviewing';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Baharu',
            self::REVIEWING => 'Dalam Semakan',
            self::RESOLVED => 'Selesai',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NEW => 'warning',
            self::REVIEWING => 'info',
            self::RESOLVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
