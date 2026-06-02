<?php
namespace App\Enums;

enum ContactType: string
{
    case CUSTOMER = 'customer';
    case SUPPLIER = 'supplier';
    case BOTH = 'both';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'مشتری',
            self::SUPPLIER => 'تامین کننده',
            self::BOTH => 'مشتری و تامین کننده',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CUSTOMER => 'primary',
            self::SUPPLIER => 'warning',
            self::BOTH => 'success',
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
