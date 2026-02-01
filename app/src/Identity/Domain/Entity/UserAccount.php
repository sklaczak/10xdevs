<?php

namespace App\Identity\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_account')]
class UserAccount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Podpinamy istniejącego Person po emailu przy rejestracji/logowaniu
    #[ORM\Column(nullable: true)]
    private ?int $personId = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column]
    private bool $enabled = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastLoginAt = null;

    private function __construct() {}

    public static function create(?int $personId = null): self
    {
        $self = new self();
        $self->personId = $personId;
        $self->createdAt = new \DateTimeImmutable();
        return $self;
    }

    public function id(): ?int { return $this->id; }
    public function personId(): ?int { return $this->personId; }
    public function roles(): array { return $this->roles; }
    public function enabled(): bool { return $this->enabled; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function lastLoginAt(): ?\DateTimeImmutable { return $this->lastLoginAt; }

    public function attachPerson(int $personId): void
    {
        $this->personId = $personId;
    }

    public function setRoles(array $roles): void
    {
        $roles = array_values(array_unique($roles));
        if ($roles === []) {
            $roles = ['ROLE_USER'];
        }
        $this->roles = $roles;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function markLoggedIn(): void
    {
        $this->lastLoginAt = new \DateTimeImmutable();
    }
}
