<?php

namespace App\Entity;

use App\Repository\WorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("workspace:read")]
    
   
    private ?int $id = null;

    #[ORM\Column]
    #[Groups("workspace:read")]
    
    private ?int $nbrPlace = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups("workspace:read")]

    private ?string $description = null;
    
    #[ORM\ManyToOne(inversedBy: 'workspaces')]
    #[Groups("workspace:read")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'workspace')]
    #[Groups("workspace:read")]
    
   
    private ?CategoryWorkspace $categoryWorkspace = null;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: ImageSave::class, cascade: ['persist'])]
    #[Groups("workspace:read")]
    
    
    private Collection $imageSaves;

    #[ORM\Column]
    #[Groups("workspace:read")]
    
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'workspace', targetEntity: Order::class)]
    #[Groups("workspace:read")]
   
    
    private Collection $orders;

    #[ORM\Column(length: 255)]
    #[Groups("workspace:read")]
    
    private ?string $nom = null;

    public function __construct()
    {
        $this->imageSaves = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbrPlace(): ?int
    {
        return $this->nbrPlace;
    }

    public function setNbrPlace(int $nbrPlace): self
    {
        $this->nbrPlace = $nbrPlace;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCategoryWorkspace(): ?CategoryWorkspace
    {
        return $this->categoryWorkspace;
    }

    public function setCategoryWorkspace(?CategoryWorkspace $categoryWorkspace): self
    {
        $this->categoryWorkspace = $categoryWorkspace;

        return $this;
    }

    /**
     * @return Collection<int, ImageSave>
     */
    public function getImageSaves(): Collection
    {
        return $this->imageSaves;
    }

    public function addImageSave(ImageSave $imageSave): self
    {
        if (!$this->imageSaves->contains($imageSave)) {
            $this->imageSaves->add($imageSave);
            $imageSave->setWorkspace($this);
        }

        return $this;
    }

    public function removeImageSave(ImageSave $imageSave): self
    {
        if ($this->imageSaves->removeElement($imageSave)) {
            // set the owning side to null (unless already changed)
            if ($imageSave->getWorkspace() === $this) {
                $imageSave->setWorkspace(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setWorkspace($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getWorkspace() === $this) {
                $order->setWorkspace(null);
            }
        }

        return $this;
    }

    public function getRemainingPlaces(): int
    {
        $placesTotales = 120;
        $nombreReservations = 0;

        foreach ($this->getOrders() as $order) {
            if (!$order->isExpired()) { // Vérifiez si la date de fin de réservation n'est pas passée
                $nombreReservations += $order->getNumberPassengers();
            }
        }

        $placesRestantes = $placesTotales - $nombreReservations;

        return $placesRestantes;
    }

    public function __toString()
    {
        return $this->categoryWorkspace;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }
}
