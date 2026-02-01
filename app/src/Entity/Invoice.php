<?php

namespace App\Entity;

use App\Enum\InvoiceType;
use App\Person\Domain\Entity\Person;
use App\Repository\InvoiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: InvoiceType::class)]
    private InvoiceType $type;

    #[ORM\Column(length: 50, unique: true)]
    private string $number ='';

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Customer $customer = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Project $project = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $netAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $grossAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $taxAmount = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2, nullable: true)]
    private ?string $taxRate = null;

    #[ORM\Column(length: 30)]
    private string $status ='';

    #[ORM\ManyToOne(targetEntity: Person::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Person $person = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $issuedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dueDate = null;

    #[ORM\ManyToOne(targetEntity: UninvoicedPurchase::class)]
    private ?UninvoicedPurchase $uninvoicedPurchase = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): InvoiceType
    {
        return $this->type;
    }

    public function setType(InvoiceType $type): self
    {
        $this->type = $type;
        return $this;
    }


    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getNetAmount(): ?string
    {
        return $this->netAmount;
    }

    public function setNetAmount(string $netAmount): self
    {
        $this->netAmount = $netAmount;
        return $this;
    }

    public function getGrossAmount(): ?string
    {
        return $this->grossAmount;
    }

    public function setGrossAmount(string $grossAmount): self
    {
        $this->grossAmount = $grossAmount;
        return $this;
    }

    public function getTaxAmount(): ?string
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(string $taxAmount): self
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getTaxRate(): ?string
    {
        return $this->taxRate;
    }

    public function setTaxRate(string $taxRate): self
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getIssuedAt(): ?\DateTimeImmutable
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeImmutable $issuedAt): self
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    public function getDueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;
        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): static
    {
        $this->project = $project;
        return $this;
    }
}
