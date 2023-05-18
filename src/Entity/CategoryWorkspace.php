<?php

namespace App\Entity;

use App\Repository\CategoryWorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryWorkspaceRepository::class)]
class CategoryWorkspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'categoryWorkspace', targetEntity: Workspace::class)]
    private Collection $workspace;

    public function __construct()
    {
        $this->workspace = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Workspace>
     */
    public function getWorkspace(): Collection
    {
        return $this->workspace;
    }

    public function addWorkspace(Workspace $workspace): self
    {
        if (!$this->workspace->contains($workspace)) {
            $this->workspace->add($workspace);
            $workspace->setCategoryWorkspace($this);
        }

        return $this;
    }

    public function removeWorkspace(Workspace $workspace): self
    {
        if ($this->workspace->removeElement($workspace)) {
            // set the owning side to null (unless already changed)
            if ($workspace->getCategoryWorkspace() === $this) {
                $workspace->setCategoryWorkspace(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}