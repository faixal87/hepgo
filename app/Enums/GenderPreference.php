<?php

namespace App\Enums;

enum GenderPreference: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case FAMILY = 'family';
    case ANY = 'any';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Lelaki',
            self::FEMALE => 'Perempuan',
            self::FAMILY => 'Keluarga',
            self::ANY => 'Terbuka',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $preference): array => [$preference->value => $preference->label()])
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
