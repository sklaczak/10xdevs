<?php

namespace App\Person\Application\Command\CreatePerson;

final class CreatePersonCommand
{
    public function __construct(
        private readonly string $fullName,
        private readonly ?string $email = null,
        private readonly ?string $phone = null,
    ) {}

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

}
