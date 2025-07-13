<?php

declare(strict_types=1);

namespace App\Actions\Import;

use App\Data\Sources\ErpProspectData;
use App\Enums\ProspectDataSource;
use Illuminate\Support\Facades\Config;

final readonly class ImportErpProspects extends AbstractImportProspectsAction
{
    protected function getDataSource(): ProspectDataSource
    {
        return ProspectDataSource::ERP;
    }

    protected function getBaseUrl(): string
    {
        return Config::string('services.erp.prospects.url');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getApiParameters(): array
    {
        return [];
    }

    protected function getResponseDataKey(): string
    {
        return 'users';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createProspectData(array $data): ErpProspectData
    {
        return ErpProspectData::from($data);
    }

    protected function supportsPagination(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPaginationParameters(int $limit, int $skip): array
    {
        return [
            'limit' => $limit,
            'skip' => $skip,
        ];
    }
}
