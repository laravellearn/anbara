<?php
namespace App\Enums;

enum InvoiceStatus:string
{
    case DRAFT = 'draft';

    case PENDING = 'pending';

    case CONFIRMED = 'confirmed';

    case PAID = 'paid';

    case PARTIAL_PAID = 'partial_paid';

    case CANCELLED = 'cancelled';

    case RETURNED = 'returned';


public function label(): string
{
    return match ($this) {

        self::DRAFT => 'پیش نویس',

        self::PENDING => 'در انتظار تایید',

        self::CONFIRMED => 'تایید شده',

        self::PAID => 'پرداخت شده',

        self::PARTIAL_PAID => 'پرداخت ناقص',

        self::CANCELLED => 'لغو شده',

        self::RETURNED => 'مرجوع شده',
    };
}

public function color(): string
{
    return match ($this) {

        self::DRAFT => 'secondary',

        self::PENDING => 'warning',

        self::CONFIRMED => 'primary',

        self::PAID => 'success',

        self::PARTIAL_PAID => 'info',

        self::CANCELLED => 'danger',

        self::RETURNED => 'dark',
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