<?php
namespace App\Enums;

enum SubscriptionStatus:string
{
    case TRIAL = 'trial';

    case ACTIVE = 'active';

    case EXPIRED = 'expired';

    case CANCELLED = 'cancelled';

    case SUSPENDED = 'suspended';


public function label(): string
{
    return match ($this) {

        self::TRIAL => 'آزمایشی',

        self::ACTIVE => 'فعال',

        self::EXPIRED => 'منقضی شده',

        self::CANCELLED => 'لغو شده',

        self::SUSPENDED => 'معلق',
    };
}

public function color(): string
{
    return match ($this) {

        self::TRIAL => 'info',

        self::ACTIVE => 'success',

        self::EXPIRED => 'danger',

        self::CANCELLED => 'secondary',

        self::SUSPENDED => 'warning',
    };
}

public static function options(): array
{
    return collect(self::cases())
        ->mapWithKeys(fn ($case) => [
            $case->value => $case->label()
        ])
        ->toArray();
}
}