<?php
namespace App\Enums;

enum PaymentStatus:string
{
    case PENDING = 'pending';

    case SUCCESS = 'success';

    case FAILED = 'failed';

    case REFUNDED = 'refunded';


public function label(): string
{
    return match ($this) {

        self::PENDING => 'در انتظار پرداخت',

        self::SUCCESS => 'موفق',

        self::FAILED => 'ناموفق',

        self::REFUNDED => 'بازگشت وجه',
    };
}

public function color(): string
{
    return match ($this) {

        self::PENDING => 'warning',

        self::SUCCESS => 'success',

        self::FAILED => 'danger',

        self::REFUNDED => 'info',
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