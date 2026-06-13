<?php

namespace App\Enums;

enum ReportType: string
{
    case FULL_HOUSE = 'full_house';
    case WRONG_LOCATION = 'wrong_location';
    case INACTIVE_PHONE = 'inactive_phone';
    case WRONG_PRICE = 'wrong_price';
    case MISLEADING_PHOTO = 'misleading_photo';
    case OWNER_NOT_RESPONDING = 'owner_not_responding';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::FULL_HOUSE => 'Rumah sudah penuh',
            self::WRONG_LOCATION => 'Lokasi salah',
            self::INACTIVE_PHONE => 'Nombor telefon tidak aktif',
            self::WRONG_PRICE => 'Harga tidak tepat',
            self::MISLEADING_PHOTO => 'Gambar mengelirukan',
            self::OWNER_NOT_RESPONDING => 'Pemilik tidak memberi respons',
            self::OTHER => 'Lain-lain',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->label()])
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
