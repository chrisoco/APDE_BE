<?php

declare(strict_types=1);

namespace App\Enums;

use App\Actions\Import\ImportErpProspects;
use App\Actions\Import\ImportKuebaProspects;
use App\Traits\HasEnumHelpers;

enum ProspectDataSource: string
{
    use HasEnumHelpers;

    case ERP = 'erp';
    case KUEBA = 'kueba';

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
