<?php

namespace App\Enums;

enum WarehouseLocationType: string
{
    case AISLE = 'aisle';
    case RACK = 'rack';
    case SHELF = 'shelf';
    case BIN = 'bin';

    public function label(): string
    {
        return match ($this) {
            self::AISLE => 'راهرو',
            self::RACK  => 'قفسه',
            self::SHELF => 'طبقه',
            self::BIN   => 'پالت',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AISLE => 'info',
            self::RACK  => 'primary',
            self::SHELF => 'secondary',
            self::BIN   => 'warning',
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