<?php

declare(strict_types=1);

namespace App\Data\Sources;

use App\Enums\ProspectDataSource;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\CamelCaseMapper;

#[MapInputName(CamelCaseMapper::class)]
final class KuebaProspectData extends Data
{
    public ProspectDataSource $source = ProspectDataSource::KUEBA;

    public function __construct(
        #[MapInputName('login.uuid')]
        public string $external_id,
        #[MapInputName('name.first')]
        public string $first_name,
        #[MapInputName('name.last')]
        public string $last_name,
        public string $email,
        public string $phone,
        public string $gender,
        #[MapInputName('dob.age')]
        public int $age,
        #[MapInputName('dob.date')]
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.v\Z')]
        public Carbon $birth_date,
        #[MapInputName('picture.large')]
        public string $image,
        #[MapInputName('location')]
        public KuebaAddressData $address,
    ) {}
}
