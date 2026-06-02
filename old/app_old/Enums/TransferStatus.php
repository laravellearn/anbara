<?php
namespace App\Enums;

enum TransferStatus:string
{
    case DRAFT = 'draft';

    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    case SENT = 'sent';

    case RECEIVED = 'received';

    case CANCELLED = 'cancelled';


public function label(): string
{
    return match ($this) {

        self::DRAFT => 'پیش نویس',

        self::PENDING => 'در انتظار تایید',

        self::APPROVED => 'تایید شده',

        self::REJECTED => 'رد شده',

        self::SENT => 'ارسال شده',

        self::RECEIVED => 'دریافت شده',

        self::CANCELLED => 'لغو شده',
    };
}

public function color(): string
{
    return match ($this) {

        self::DRAFT => 'secondary',

        self::PENDING => 'warning',

        self::APPROVED => 'success',

        self::REJECTED => 'danger',

        self::SENT => 'info',

        self::RECEIVED => 'primary',

        self::CANCELLED => 'dark',
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