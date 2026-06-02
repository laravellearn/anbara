<?php
namespace App\Enums;

enum ActivityAction: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case RESTORE = 'restore';
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case EXPORT = 'export';
    case IMPORT = 'import';

    public function label(): string
    {
        return match ($this) {
            self::CREATE => 'ایجاد',
            self::UPDATE => 'ویرایش',
            self::DELETE => 'حذف',
            self::RESTORE => 'بازیابی',
            self::LOGIN => 'ورود',
            self::LOGOUT => 'خروج',
            self::EXPORT => 'خروجی',
            self::IMPORT => 'ورود اطلاعات',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CREATE => 'success',
            self::UPDATE => 'info',
            self::DELETE => 'danger',
            self::RESTORE => 'primary',
            self::LOGIN => 'success',
            self::LOGOUT => 'secondary',
            self::EXPORT => 'warning',
            self::IMPORT => 'primary',
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
