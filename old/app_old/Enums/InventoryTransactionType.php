<?php
namespace App\Enums;

enum InventoryTransactionType: string
{
    case OPENING = 'opening';
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case ADJUSTMENT_IN = 'adjustment_in';
    case ADJUSTMENT_OUT = 'adjustment_out';
    case RETURN_PURCHASE = 'return_purchase';
    case RETURN_SALE = 'return_sale';
    case ASSET_ASSIGN = 'asset_assign';
    case ASSET_RETURN = 'asset_return';
    case SCRAP = 'scrap';

    public function label(): string
    {
        return match ($this) {
            self::OPENING => 'موجودی اولیه',
            self::PURCHASE => 'خرید',
            self::SALE => 'فروش',
            self::TRANSFER_IN => 'انتقال ورودی',
            self::TRANSFER_OUT => 'انتقال خروجی',
            self::ADJUSTMENT_IN => 'اصلاح افزایشی',
            self::ADJUSTMENT_OUT => 'اصلاح کاهشی',
            self::RETURN_PURCHASE => 'مرجوعی خرید',
            self::RETURN_SALE => 'مرجوعی فروش',
            self::ASSET_ASSIGN => 'تحویل اموال',
            self::ASSET_RETURN => 'عودت اموال',
            self::SCRAP => 'اسقاط',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPENING => 'secondary',
            self::PURCHASE => 'success',
            self::SALE => 'danger',
            self::TRANSFER_IN => 'info',
            self::TRANSFER_OUT => 'warning',
            self::ADJUSTMENT_IN => 'success',
            self::ADJUSTMENT_OUT => 'danger',
            self::RETURN_PURCHASE => 'primary',
            self::RETURN_SALE => 'primary',
            self::ASSET_ASSIGN => 'warning',
            self::ASSET_RETURN => 'success',
            self::SCRAP => 'dark',
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
