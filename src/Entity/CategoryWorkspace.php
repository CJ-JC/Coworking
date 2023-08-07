<?php

namespace App\Entity;

use App\Repository\CategoryWorkspaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: CategoryWorkspaceRepository::class)]
class CategoryWorkspace
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("workspace:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("workspace:read")]
    private ?string $title = null;

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