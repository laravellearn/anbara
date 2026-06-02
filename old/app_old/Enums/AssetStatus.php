<?php
namespace App\Enums;

enum AssetStatus: string
{
    case AVAILABLE = 'available';
    case ASSIGNED = 'assigned';
    case REPAIR = 'repair';
    case LOST = 'lost';
    case SCRAPPED = 'scrapped';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'آزاد',
            self::ASSIGNED => 'تحویل شده',
            self::REPAIR => 'در تعمیر',
            self::LOST => 'مفقود',
            self::SCRAPPED => 'اسقاط شده',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::ASSIGNED => 'primary',
            self::REPAIR => 'warning',
            self::LOST => 'danger',
            self::SCRAPPED => 'dark',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}
