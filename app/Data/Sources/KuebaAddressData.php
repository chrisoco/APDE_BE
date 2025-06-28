<?php

declare(strict_types=1);

namespace App\Data\Sources;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Hidden;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
final class KuebaAddressData extends Data
{
    #[Computed]
    public string $address;

    public function __construct(
        #[MapInputName('street.name')]
        #[Hidden]
        public string $street_name,
        #[MapInputName('street.number')]
        #[Hidden]
        public int $street_number,
        public string $city,
        public string $state,
        #[MapInputName('postcode')]
        public string $postal_code,
        public string $country,
        #[MapInputName('coordinates.latitude')]
        public float $latitude,
        #[MapInputName('coordinates.longitude')]
        public float $longitude,
    ) {
        $this->address = "{$street_name} {$street_number}";
    }
}
