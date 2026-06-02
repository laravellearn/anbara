<?php
namespace App\Enums;

enum AssetAssignmentStatus: string
{
    case ASSIGNED = 'assigned';

    case RETURNED = 'returned';

    case LOST = 'lost';


    public function label(): string
    {
        return match ($this) {

            self::ASSIGNED => 'تحویل شده',

            self::RETURNED => 'عودت داده شده',

            self::LOST => 'مفقود شده',
        };
    }

    public function color(): string
    {
        return match ($this) {

            self::ASSIGNED => 'primary',

            self::RETURNED => 'success',

            self::LOST => 'danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => $case->label()
            ])
            ->toArray();
    }
}
