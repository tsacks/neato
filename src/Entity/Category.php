<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use Timestamps;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $title = null;

    #[ORM\Column(length: 60)]
    private ?string $slug = null;

    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'parent')]
    private Collection $children;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id')]
    private Category|null $parent = null;

    #[ORM\OneToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'redirect_id', referencedColumnName: 'id')]
    private Category|null $redirect = null;

    /**
     * @var Collection<int, Link>
     */
    #[ORM\ManyToMany(targetEntity: Link::class, mappedBy: 'categories')]
    private Collection $links;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $news_slug = null;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->links = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $Title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChildren(Category $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }
        $this->children = $children;

        return $this;
    }

    public function removeChildren(Category $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(Category $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function getRedirect(): Category
    {
        return $this->redirect;
    }

    public function setRedirect(Category $redirect): static
    {
        $this->redirect = $redirect;

        return $this;
    }

    /**
     * @return Collection<int, Link>
     */
    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function addLink(Link $link): static
    {
        if (!$this->links->contains($link)) {
            $this->links->add($link);
            $link->addCategory($this);
        }

        return $this;
    }

    public function removeLink(Link $link): static
    {
        if ($this->links->removeElement($link)) {
            $link->removeCategory($this);
        }

        return $this;
    }

    public function getNewsSlug(): ?string
    {
        return $this->news_slug;
    }

    public function setNewsSlug(?string $news_slug): static
    {
        $this->news_slug = $news_slug;

        return $this;
    }
}
