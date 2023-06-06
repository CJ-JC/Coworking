<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Column(length: 255)]
    // private ?string $title = null;

    // #[ORM\Column(length: 255)]
    // private ?string $price = null;

    // #[ORM\Column(type: Types::TEXT)]
    // private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    // #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    // private ?\DateTimeInterface $orderHour = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Workspace $workspace = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?Subscription $subscription = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberPassengers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    // public function getTitle(): ?string
    // {
    //     return $this->title;
    // }

    // public function setTitle(string $title): self
    // {
    //     $this->title = $title;

    //     return $this;
    // }

    // public function getPrice(): ?string
    // {
    //     return $this->price;
    // }

    // public function setPrice(string $price): self
    // {
    //     $this->price = $price;

    //     return $this;
    // }

    // public function getDescription(): ?string
    // {
    //     return $this->description;
    // }

    // public function setDescription(string $description): self
    // {
    //     $this->description = $description;

    //     return $this;
    // }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getSubscription(): ?Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(?Subscription $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function getNumberPassengers(): ?int
    {
        return $this->numberPassengers;
    }

    public function setNumberPassengers(?int $numberPassengers): self
    {
        $this->numberPassengers = $numberPassengers;

        return $this;
    }

    public function isExpired(): bool
    {
        $endDate = $this->getEndDate(); // Obtenez la date de fin de réservation de l'entité Order
        $today = new \DateTime(); // Obtenez la date actuelle

        return $endDate < $today; // Vérifiez si la date de fin de réservation est inférieure à la date actuelle
    }
}
