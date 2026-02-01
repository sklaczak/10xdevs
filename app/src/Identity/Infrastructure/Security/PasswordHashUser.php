<?php

namespace App\Identity\Infrastructure\Security;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class PasswordHashUser implements PasswordAuthenticatedUserInterface
{
    public function __construct(private ?string $passwordHash = null) {}

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }
}
