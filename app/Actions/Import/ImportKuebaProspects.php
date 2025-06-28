<?php

declare(strict_types=1);

namespace App\Actions\Import;

use App\Data\Sources\KuebaProspectData;
use App\Enums\ProspectDataSource;
use Illuminate\Support\Facades\Config;

final readonly class ImportKuebaProspects extends AbstractImportProspectsAction
{
    protected function getDataSource(): ProspectDataSource
    {
        return ProspectDataSource::KUEBA;
    }

    protected function getBaseUrl(): string
    {
        return Config::string('services.kueba.prospects.url');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getApiParameters(): array
    {
        return [
            'nat' => 'ch',
            'results' => 100,
        ];
    }

    protected function getResponseDataKey(): string
    {
        return 'results';
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createProspectData(array $data): KuebaProspectData
    {
        return KuebaProspectData::from($data);
    }

    protected function supportsPagination(): bool
    {
        return false;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getPaginationParameters(int $limit, int $skip): array
    {
        return [];
    }
}
