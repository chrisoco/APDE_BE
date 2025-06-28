<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\Sources\KuebaProspectData;
use App\Enums\ProspectDataSource;
use App\Models\Prospect;
use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Spatie\LaravelData\Exceptions\CannotCreateData;

final readonly class ImportKuebaProspects
{
    /**
     * Execute the action.
     */
    public function handle(): void
    {

        $fetchedExternalIds = [];

        // Fetch and process all prospects
        foreach ($this->fetchAllProspects() as $prospect) {

            $fetchedExternalIds[] = $prospect->external_id;

            Prospect::firstOrCreate(
                ['email' => $prospect->email],
                $prospect->toArray()
            );
        }

        // Soft delete prospects that weren't fetched but exist in the database
        if ($fetchedExternalIds !== []) {
            Prospect::where('source', ProspectDataSource::KUEBA)
                ->whereNotNull('external_id')
                ->whereNotIn('external_id', $fetchedExternalIds)
                ->delete();
        }
    }

    /**
     * @return Generator<KuebaProspectData>
     */
    private function fetchAllProspects(): Generator
    {
        $url = config('services.kueba.prospects.url');

        throw_unless(is_string($url), new RuntimeException('Invalid Küba prospects URL configuration.'));

        $response = Http::get($url, [
            'nat' => 'ch',
            'results' => 100,
        ]);

        throw_unless($response->successful(), new RuntimeException('Failed to fetch Küba prospects from external API.'));

        /** @var array<string, mixed> $data */
        $data = $response->json();

        throw_if(! isset($data['results']) || ! is_array($data['results']), new RuntimeException('Invalid response structure from external API.'));

        /** @var array<int, array<string, mixed>> $prospects */
        $prospects = $data['results'];

        foreach ($prospects as $prospect) {
            foreach ($prospects as $prospect) {
                try {
                    yield KuebaProspectData::from($prospect);
                } catch (CannotCreateData) {
                    continue;
                }
            }
        }
    }
}
