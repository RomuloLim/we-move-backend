<?php

namespace Modules\Operation\DTOs;

use App\Contracts\DtoContract;
use Illuminate\Support\Collection;

readonly class InstitutionDto implements DtoContract
{
    public function __construct(
        public string $name,
        public ?string $acronym = null,
        public ?string $street = null,
        public ?string $number = null,
        public ?string $complement = null,
        public ?string $neighborhood = null,
        public string $city = '',
        public string $state = '',
        public ?string $zip_code = null,
    ) {}

    public static function collection(array $data): Collection
    {
        $dtos = array_map(function ($institution) {
            return new InstitutionDto(
                name: data_get($institution, 'name'),
                acronym: data_get($institution, 'acronym'),
                street: data_get($institution, 'street'),
                number: data_get($institution, 'number'),
                complement: data_get($institution, 'complement'),
                neighborhood: data_get($institution, 'neighborhood'),
                city: data_get($institution, 'city'),
                state: data_get($institution, 'state'),
                zip_code: data_get($institution, 'zip_code'),
            );
        }, $data);

        return new Collection($dtos);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'acronym' => $this->acronym,
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
        ];
    }
}
