<?php

namespace App\Enums;

enum InventoryTransactionStatus: string
{
    case DRAFT = 'draft';

    case PENDING = 'pending';

    case APPROVED = 'approved';

    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {

            self::DRAFT => 'پیش نویس',

            self::PENDING => 'در انتظار تایید',

            self::APPROVED => 'تایید شده',

            self::REJECTED => 'رد شده',
        };
    }

    public function color(): string
    {
        return match ($this) {

            self::DRAFT => 'secondary',

            self::PENDING => 'warning',

            self::APPROVED => 'success',

            self::REJECTED => 'danger',
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