<?php


namespace App\Person\Application\Command\UpdatePerson;

final class UpdatePersonCommand
{
    public function __construct(
        public int $id,
        public string $fullName,
        public ?string $email = null,
        public ?string $phone = null,
    ) {}
}
