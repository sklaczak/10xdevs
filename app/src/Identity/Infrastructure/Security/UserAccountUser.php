<?php

namespace App\Identity\Infrastructure\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class UserAccountUser implements UserInterface
{
    public function __construct(
        private int $id,
        private array $roles,
        private bool $enabled = true,
    ) {}

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function eraseCredentials(): void {}
}
