<?php

namespace App\Identity\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'password_credential')]
class PasswordCredential
{
    #[ORM\Id]
    #[ORM\Column(name: 'user_account_id')]
    private int $userAccountId;

    #[ORM\Column(length: 255)]
    private string $passwordHash;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(int $userAccountId, string $passwordHash): self
    {
        $self = new self();
        $self->userAccountId = $userAccountId;
        $self->passwordHash = $passwordHash;
        $self->updatedAt = new \DateTimeImmutable();
        return $self;
    }

    public function userAccountId(): int { return $this->userAccountId; }
    public function passwordHash(): string { return $this->passwordHash; }
    public function updatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function changeHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
