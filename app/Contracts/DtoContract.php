<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface DtoContract
{
    /**
     * Create a collection of DTOs from an array of data.
     */
    public static function collection(array $data): Collection;

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array;
}
