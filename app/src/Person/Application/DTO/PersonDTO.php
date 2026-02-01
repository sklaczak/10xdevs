<?php

namespace App\Person\Application\DTO;

final class PersonDTO
{
    public function __construct(
        public int $id,
        public string $fullName,
        public ?string $email,
        public ?string $phone,
    ) {}

    public function displayName(): string
    {
        return $this->fullName;
    }
}
