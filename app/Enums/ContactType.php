<?php

namespace App\Enums;

enum ContactType: string
{
    case CUSTOMER = 'customer';
    case SUPPLIER = 'supplier';
    case BOTH = 'both';
    case EMPLOYEE = 'employee';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'مشتری',
            self::SUPPLIER => 'تأمین‌کننده',
            self::BOTH => 'هر دو',
            self::EMPLOYEE => 'کارمند',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CUSTOMER => 'primary',
            self::SUPPLIER => 'warning',
            self::BOTH => 'success',
            self::EMPLOYEE => 'info',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => $case->label(),
            ])
            ->toArray();
    }
}
