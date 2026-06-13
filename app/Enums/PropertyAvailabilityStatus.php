<?php

namespace App\Enums;

enum PropertyAvailabilityStatus: string
{
    case AVAILABLE = 'available';
    case FULL = 'full';
    case PENDING = 'pending';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Masih Kosong',
            self::FULL => 'Telah Penuh',
            self::PENDING => 'Menunggu Semakan',
            self::INACTIVE => 'Tidak Aktif',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::FULL => 'danger',
            self::PENDING => 'warning',
            self::INACTIVE => 'gray',
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
