<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("workspace:read")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups("workspace:read")]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups("workspace:read")]
    private array $roles = [];

    /**
     * @var string The hashed password
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)\S{6,}$/",
     *     message="Votre mot de passe doit contenir : au minimum 6 caractères, un caractère majuscule, un caractère minuscule et un chiffre"
     * )
     */
    #[ORM\Column]
    #[Groups("workspace:read")]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Workspace::class)]
    
    private Collection $workspaces;

    #[ORM\ManyToOne(inversedBy: 'user')]
    private ?Subscription $subscription = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class, cascade: ['persist', 'remove'])]
    private Collection $orders;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Contact $contact = null;

    /**
     * @Assert\Length(min=2, minMessage = "Ce prénom est trop court, minimum 2 lettres")
     * @Assert\Regex(pattern="/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/",message="Ce prénom ne doit pas contenir de caractères spéciaux")
     */
    #[ORM\Column(length: 255)]
    private ?string $firstname = null;
    
    /**
     * @Assert\Length(min=2, minMessage = "Ce prénom est trop court, minimum 2 lettres")
     * @Assert\Regex(pattern="/^[a-zA-ZÀ-ÖØ-öø-ÿ]+$/",message="Ce prénom ne doit pas contenir de caractères spéciaux")
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    /**
     * @Assert\Regex(pattern="/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/", message="Le format ne correspond pas")
     * @Assert\Length(min =10, minMessage = "Numéro invalide")
     */
    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->workspaces = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getWorkspaces(): Collection
    {
        return $this->workspaces;
    }

    public function addWorkspace(Workspace $workspace): self
    {
        if (!$this->workspaces->contains($workspace)) {
            $this->workspaces->add($workspace);
            $workspace->setUser($this);
        }

        return $this;
    }

    public function removeWorkspace(Workspace $workspace): self
    {
        if ($this->workspaces->removeElement($workspace)) {
            // set the owning side to null (unless already changed)
            if ($workspace->getUser() === $this) {
                $workspace->setUser(null);
            }
        }

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
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        // unset the owning side of the relation if necessary
        if ($contact === null && $this->contact !== null) {
            $this->contact->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($contact !== null && $contact->getUser() !== $this) {
            $contact->setUser($this);
        }

        $this->contact = $contact;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }


    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }


    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function hasNonExpiredReservations(): bool
    {
        foreach ($this->orders as $order) {
            if (!$order->isExpired()) {
                return true; // Si une réservation non expirée est trouvée, retourne true.
            }
        }
        return false; // Si aucune réservation non expirée n'est trouvée, retourne false.
    }

}
