<?php
namespace App\Enums;

enum OrganizationType:string
{
    case HEAD_OFFICE = 'head_office';

    case BRANCH = 'branch';

    case DEPARTMENT = 'department';

    case WAREHOUSE = 'warehouse';


public function label(): string
{
    return match ($this) {

        self::HEAD_OFFICE => 'دفتر مرکزی',

        self::BRANCH => 'شعبه',

        self::DEPARTMENT => 'واحد سازمانی',

        self::WAREHOUSE => 'انبار',
    };
}

public function color(): string
{
    return match ($this) {

        self::HEAD_OFFICE => 'primary',

        self::BRANCH => 'info',

        self::DEPARTMENT => 'warning',

        self::WAREHOUSE => 'success',
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