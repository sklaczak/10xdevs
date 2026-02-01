<?php

namespace App\Entity;

use App\Enum\UninvoicedPurchaseStatus;
use App\Person\Domain\Entity\Person;
use App\Repository\UninvoicedPurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UninvoicedPurchaseRepository::class)]
class UninvoicedPurchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Kwota zakupu (brutto)
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private ?string $amount = null;

    // Forma płatności – relacja ManyToOne
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?PaymentMethod $paymentMethod = null;

    // Osoba dokonująca zakupu
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    // Opis zakupu (np. "Materiały z Castoramy")
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Data zakupu
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $expenseDate = null;

    // Powiązane faktury kosztowe
    #[ORM\OneToMany(mappedBy: 'uninvoicedPurchase', targetEntity: Invoice::class)]
    private Collection $costInvoices;

    // Status rozliczenia
    #[ORM\Column(length: 30, enumType: UninvoicedPurchaseStatus::class)]
    private UninvoicedPurchaseStatus $status = UninvoicedPurchaseStatus::PENDING;

    // Meta
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->costInvoices = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    // GETTERY / SETTERY

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?PaymentMethod $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getExpenseDate(): ?\DateTimeImmutable
    {
        return $this->expenseDate;
    }

    public function setExpenseDate(\DateTimeImmutable $expenseDate): static
    {
        $this->expenseDate = $expenseDate;
        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getCostInvoices(): Collection
    {
        return $this->costInvoices;
    }

    public function addCostInvoice(Invoice $invoice): static
    {
        if (!$this->costInvoices->contains($invoice)) {
            $this->costInvoices->add($invoice);
            $invoice->setUninvoicedPurchase($this);
        }

        return $this;
    }

    public function removeCostInvoice(Invoice $invoice): static
    {
        if ($this->costInvoices->removeElement($invoice)) {
            if ($invoice->getUninvoicedPurchase() === $this) {
                $invoice->setUninvoicedPurchase(null);
            }
        }

        return $this;
    }

    public function getStatus(): UninvoicedPurchaseStatus
    {
        return $this->status;
    }

    public function setStatus(UninvoicedPurchaseStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return 'Zakup #' . $this->id;
    }
}
