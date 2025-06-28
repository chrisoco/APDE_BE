<?php

declare(strict_types=1);

namespace App\Enums;

enum DataSource: string
{
    case ERP = 'erp';
    case KUBA = 'kuba';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<int, string>
     */
    public static function labels(): array
    {
        return array_map(fn ($case): string => $case->label(), self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::ERP => 'ERP',
            self::KUBA => 'Küba',
        };
    }
}
