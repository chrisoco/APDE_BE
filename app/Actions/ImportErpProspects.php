<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\DataSource;
use App\Models\Prospect;
use Generator;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final readonly class ImportErpProspects
{
    /**
     * Execute the action.
     */
    public function handle(): void
    {
        $fetchedExternalIds = [];

        // Fetch and process all prospects
        foreach ($this->fetchAllProspects() as $prospect) {
            if ($prospect['email']) {
                $externalId = $prospect['id'] ?? null;
                if ($externalId) {
                    $fetchedExternalIds[] = $externalId;
                }

                Prospect::firstOrCreate(
                    ['email' => $prospect['email']],
                    $this->mapApiProspect($prospect)
                );
            }
        }

        // Soft delete prospects that weren't fetched but exist in the database
        if ($fetchedExternalIds !== []) {
            Prospect::where('source', DataSource::ERP)
                ->whereNotNull('external_id')
                ->whereNotIn('external_id', $fetchedExternalIds)
                ->delete();
        }
    }

    /**
     * @return Generator<array<string, mixed>>
     */
    private function fetchAllProspects(): Generator
    {
        $url = config('services.erp.prospects.url');

        throw_unless(is_string($url), new RuntimeException('Invalid ERP prospects URL configuration.'));

        $limit = 10;
        $skip = 0;

        do {
            $response = Http::get($url, [
                'limit' => $limit,
                'skip' => $skip,
            ]);

            throw_unless($response->successful(), new RuntimeException('Failed to fetch prospects from external API.'));

            /** @var array<string, mixed> $data */
            $data = $response->json();

            throw_if(! isset($data['users']) || ! is_array($data['users']), new RuntimeException('Invalid response structure from external API.'));

            /** @var array<int, array<string, mixed>> $prospects */
            $prospects = $data['users'];
            $total = $data['total'] ?? null;

            foreach ($prospects as $prospect) {
                yield $prospect;
            }

            $skip += $limit;
        } while ($total !== null && $skip < $total);
    }

    /**
     * @param  array<string, mixed>  $apiProspect
     * @return array<string, mixed>
     */
    private function mapApiProspect(array $apiProspect): array
    {
        /** @var array<string, mixed> $hair */
        $hair = $apiProspect['hair'] ?? [];

        return [
            'external_id' => $apiProspect['id'] ?? null,
            'first_name' => $apiProspect['firstName'] ?? null,
            'last_name' => $apiProspect['lastName'] ?? null,
            'maiden_name' => $apiProspect['maidenName'] ?? null,
            'age' => $apiProspect['age'] ?? null,
            'gender' => $apiProspect['gender'] ?? null,
            'email' => $apiProspect['email'] ?? null,
            'phone' => $apiProspect['phone'] ?? null,
            'username' => $apiProspect['username'] ?? null,
            'birth_date' => $apiProspect['birthDate'] ?? null,
            'image' => $apiProspect['image'] ?? null,
            'blood_group' => $apiProspect['bloodGroup'] ?? null,
            'height' => $apiProspect['height'] ?? null,
            'weight' => $apiProspect['weight'] ?? null,
            'eye_color' => $apiProspect['eyeColor'] ?? null,
            'hair_color' => $hair['color'] ?? null,
            'hair_type' => $hair['type'] ?? null,
            'address' => $apiProspect['address'] ?? null,
            'university' => $apiProspect['university'] ?? null,
            'bank' => $apiProspect['bank'] ?? null,
            'company' => $apiProspect['company'] ?? null,
            'ein' => $apiProspect['ein'] ?? null,
            'ssn' => $apiProspect['ssn'] ?? null,
            'role' => $apiProspect['role'] ?? null,
            'source' => DataSource::ERP,
        ];
    }
}
