<?php

namespace Modules\Operation\DTOs;

class InstitutionDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $acronym = null,
        public readonly ?string $street = null,
        public readonly ?string $number = null,
        public readonly ?string $complement = null,
        public readonly ?string $neighborhood = null,
        public readonly string $city = '',
        public readonly string $state = '',
        public readonly ?string $zip_code = null,
    ) {}

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
