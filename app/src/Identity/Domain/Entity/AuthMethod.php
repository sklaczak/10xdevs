<?php

namespace App\Identity\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'auth_method')]
#[ORM\UniqueConstraint(name: 'uniq_auth_type_identifier', columns: ['type', 'identifier'])]
#[ORM\Index(name: 'idx_auth_user', columns: ['user_account_id'])]
class AuthMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'user_account_id')]
    private int $userAccountId;

    #[ORM\Column(length: 50)]
    private string $type;

    // Google: sub; Password: email; Mobile: sub; Passkey: credentialId
    #[ORM\Column(length: 255)]
    private string $identifier;

    #[ORM\Column(type: 'json')]
    private array $metadata = [];

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastUsedAt = null;

    private function __construct() {}

    public static function create(int $userAccountId, string $type, string $identifier, array $metadata = []): self
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            throw new \DomainException('Auth identifier jest wymagany.');
        }

        $self = new self();
        $self->userAccountId = $userAccountId;
        $self->type = $type;
        $self->identifier = $identifier;
        $self->metadata = $metadata;
        $self->createdAt = new \DateTimeImmutable();

        return $self;
    }

    public function id(): ?int { return $this->id; }
    public function userAccountId(): int { return $this->userAccountId; }
    public function type(): string { return $this->type; }
    public function identifier(): string { return $this->identifier; }
    public function metadata(): array { return $this->metadata; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }
    public function lastUsedAt(): ?\DateTimeImmutable { return $this->lastUsedAt; }

    public function markUsed(): void
    {
        $this->lastUsedAt = new \DateTimeImmutable();
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
