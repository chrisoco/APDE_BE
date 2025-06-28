<?php

declare(strict_types=1);

namespace App\Enums;

use App\Actions\Import\ImportErpProspects;
use App\Actions\Import\ImportKuebaProspects;

enum ProspectDataSource: string
{
    case ERP = 'erp';
    case KUEBA = 'kueba';

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
            self::KUEBA => 'Küba',
        };
    }

    /**
     * Get the import action class name for the data source.
     *
     * @return class-string
     */
    public function importAction(): string
    {
        return match ($this) {
            self::ERP => ImportErpProspects::class,
            self::KUEBA => ImportKuebaProspects::class,
        };
    }
}
