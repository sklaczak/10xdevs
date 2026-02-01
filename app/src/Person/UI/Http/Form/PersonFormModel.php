<?php

namespace App\Person\UI\Http\Form;

final class PersonFormModel
{
    public function __construct(
        public string $fullName = '',
        public ?string $email = null,
        public ?string $phone = null,
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
