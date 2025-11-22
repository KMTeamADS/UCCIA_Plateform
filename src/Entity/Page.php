<?php

declare (strict_types = 1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Enums\PageType;
use ADS\UCCIA\Entity\Traits\WithEnable;
use ADS\UCCIA\Entity\Traits\WithTimestamps;
use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\PageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_pages')]
#[ORM\Entity(repositoryClass: PageRepository::class)]
class Page implements TranslatableInterface
{
    use WithUuid;
    use TranslatableTrait;
    use WithTimestamps;
    use WithEnable;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50, enumType: PageType::class, options: ['default' => PageType::STANDARD])]
    private PageType $type = PageType::STANDARD;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $children;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->children = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->translate()->getName();
    }

    /** @return PageTranslation */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    public function getName(): string
    {
        return $this->translate()->getName();
    }

    public function getType(): PageType
    {
        return $this->type;
    }

    public function setType(PageType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isKnot(): bool
    {
        return $this->getType() === PageType::KNOT || $this->getChildren()->count() > 0;
    }

    public function getUrl(): string
    {
        $url = '';

        foreach ($this->getParents() as $parent) {
            $url .= $parent->translate()->getSlug() . '/';
        }

        return $url . $this->translate()->getSlug();
    }

    /** @return self[] */
    public function getParents(): array
    {
        $parent = $this->getParent();
        $parents = [];

        while ($parent !== null) {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }

        return array_reverse($parents);
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        // Ensure bidirectional relation is respected.
        if ($parent && false === $parent->getChildren()->indexOf($this)) {
            $parent->addChild($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }
}
