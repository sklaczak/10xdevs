<?php

namespace App\Person\Domain\Entity;

use App\Shared\Domain\Exception\DomainException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'person')]
class Person
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $fullName = '';

    #[ORM\Column(length: 255, unique: true)]
    private string $email = '';

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phone = null;

    private function __construct() {}

    public static function create(
        string $fullName,
        ?string $email,
        ?string $phone
    ): self
    {
        $self = new self();
        $self->rename($fullName);
        $self->changeContact($email, $phone);

        return $self;
    }

    public function id(): ?int {
        return $this->id;
    }
    public function fullName(): string {
        return $this->fullName;
    }
    public function email(): ?string {
        return $this->email;
    }
    public function phone(): ?string {
        return $this->phone;
    }

    public function displayName(): string
    {
        return $this->fullName;
    }

    public function rename(string $fullName): void
    {
        $this->fullName = trim($fullName);
    }

    public function changeContact(?string $email, ?string $phone): void
    {
        $email = $email !== null ? trim($email) : null;
        $phone = $phone !== null ? trim($phone) : null;

        if ($email === '') $email = null;
        if ($phone === '') $phone = null;

        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new DomainException('Niepoprawny email.');
        }

        $this->email = $email;
        $this->phone = $phone;
    }
}
