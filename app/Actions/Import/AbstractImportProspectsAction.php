<?php

declare(strict_types=1);

namespace App\Actions\Import;

use App\Enums\ProspectDataSource;
use App\Models\Prospect;
use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Exceptions\CannotCreateData;

abstract readonly class AbstractImportProspectsAction
{
    abstract protected function getDataSource(): ProspectDataSource;

    abstract protected function getBaseUrl(): string;

    /**
     * @return array<string, mixed>
     */
    abstract protected function getApiParameters(): array;

    abstract protected function getResponseDataKey(): string;

    /**
     * @param  array<string, mixed>  $data
     */
    abstract protected function createProspectData(array $data): Data;

    abstract protected function supportsPagination(): bool;

    /**
     * @return array<string, mixed>
     */
    abstract protected function getPaginationParameters(int $limit, int $skip): array;

    /**
     * Execute the action.
     */
    final public function handle(): void
    {
        $fetchedExternalIds = [];

        foreach ($this->fetchAllProspects() as $prospectDto) {
            /** @phpstan-ignore-next-line */
            $fetchedExternalIds[] = $prospectDto->external_id;

            // Find existing prospect (including trashed) by email
            /** @phpstan-ignore-next-line */
            $prospect = Prospect::withTrashed()->where('email', $prospectDto->email)->first();

            if ($prospect) {

                $prospect->update($prospectDto->toArray());

                if ($prospect->trashed()) {
                    $prospect->restore();
                }

            } else {
                // Create new prospect
                Prospect::create($prospectDto->toArray());
            }
        }

        if ($fetchedExternalIds !== []) {
            Prospect::where('source', $this->getDataSource())
                ->whereNotNull('external_id')
                ->whereNotIn('external_id', $fetchedExternalIds)
                ->delete();
        }
    }

    /**
     * @return Generator<Data>
     */
    private function fetchAllProspects(): Generator
    {
        $url = $this->getBaseUrl();

        throw_if($url === '', new RuntimeException("Invalid {$this->getDataSource()->label()} prospects URL configuration."));

        if ($this->supportsPagination()) {
            yield from $this->fetchWithPagination($url);
        } else {
            yield from $this->fetchWithoutPagination($url);
        }
    }

    /**
     * @return Generator<Data>
     */
    private function fetchWithPagination(string $url): Generator
    {
        $limit = 50;
        $skip = 0;

        do {
            $response = Http::get($url, $this->getPaginationParameters($limit, $skip));

            throw_unless($response->successful(), new RuntimeException("Failed to fetch {$this->getDataSource()->label()} prospects from external API."));

            /** @var array<string, mixed> $data */
            $data = $response->json();

            throw_if(! isset($data[$this->getResponseDataKey()]) || ! is_array($data[$this->getResponseDataKey()]), new RuntimeException('Invalid response structure from external API.'));

            /** @var array<int, array<string, mixed>> $prospects */
            $prospects = $data[$this->getResponseDataKey()];
            $total = $data['total'] ?? null;

            foreach ($prospects as $prospect) {
                try {
                    yield $this->createProspectData($prospect);
                } catch (CannotCreateData) {
                    continue;
                }
            }

            $skip += $limit;
        } while ($total !== null && $skip < $total);
    }

    /**
     * @return Generator<Data>
     */
    private function fetchWithoutPagination(string $url): Generator
    {
        $response = Http::get($url, $this->getApiParameters());

        throw_unless($response->successful(), new RuntimeException("Failed to fetch {$this->getDataSource()->label()} prospects from external API."));

        /** @var array<string, mixed> $data */
        $data = $response->json();

        throw_if(! isset($data[$this->getResponseDataKey()]) || ! is_array($data[$this->getResponseDataKey()]), new RuntimeException('Invalid response structure from external API.'));

        /** @var array<int, array<string, mixed>> $prospects */
        $prospects = $data[$this->getResponseDataKey()];

        foreach ($prospects as $prospect) {
            try {
                yield $this->createProspectData($prospect);
            } catch (CannotCreateData) {
                continue;
            }
        }
    }
}
