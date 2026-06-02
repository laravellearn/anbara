<?php

namespace App\Enums;
enum HistoryAction: string
{
    case OPENING = 'opening';
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case TRANSFER = 'transfer';
    case DELIVERY = 'delivery';
    case RETURNED = 'returned';
    case ADJUSTMENT = 'adjustment';
    case SCRAP = 'scrap';
    case EDIT = 'edit';

    public function label(): string
    {
        return match ($this) {
            self::OPENING => 'موجودی اولیه',
            self::PURCHASE => 'خرید',
            self::SALE => 'فروش',
            self::TRANSFER => 'انتقال',
            self::DELIVERY => 'تحویل',
            self::RETURNED => 'مرجوعی',
            self::ADJUSTMENT => 'اصلاح موجودی',
            self::SCRAP => 'اسقاط',
            self::EDIT => 'ویرایش',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPENING => 'secondary',
            self::PURCHASE => 'success',
            self::SALE => 'danger',
            self::TRANSFER => 'info',
            self::DELIVERY => 'primary',
            self::RETURNED => 'warning',
            self::ADJUSTMENT => 'dark',
            self::SCRAP => 'danger',
            self::EDIT => 'secondary',
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
