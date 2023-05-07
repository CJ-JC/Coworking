<?php

namespace App\Entity;

use App\Repository\WorkspaceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkspaceRepository::class)]
class Workspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nbrPlace = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'workspaces')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'workspace')]
    private ?CategoryWorkspace $categoryWorkspace = null;

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
}
