<?php

namespace App\Identity\Application\DTO;

final class CurrentUserDTO
{
    public function __construct(
        public int $userAccountId,
        public ?int $personId,
        public array $roles,
        public bool $enabled,
    ) {}
}
