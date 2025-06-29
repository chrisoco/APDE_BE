<?php

declare(strict_types=1);

namespace App\Data\Sources;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
final class ErpAddressData extends Data
{
    public function __construct(
        public string $address,
        public string $city,
        public string $state,
        #[MapInputName('postalCode')]
        public string $plz,
        public string $country,
        #[MapInputName('coordinates.lat')]
        public float $latitude,
        #[MapInputName('coordinates.lng')]
        public float $longitude,
    ) {}
}
