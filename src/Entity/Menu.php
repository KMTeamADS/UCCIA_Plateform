<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Traits\WithEnable;
use ADS\UCCIA\Entity\Traits\WithTimestamps;
use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_menus')]
#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_INTERNAL_NAME', columns: ['internal_name'])]
#[UniqueEntity(fields: ['internalName'])]
class Menu implements TranslatableInterface
{
    use WithUuid;
    use TranslatableTrait;
    use WithTimestamps;
    use WithEnable;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    #[Assert\Regex(pattern: '/^[a-z0-9\_]+$/', htmlPattern: '[a-z0-9\_]+')]
    #[ORM\Column(length: 255, unique: true)]
    private string $internalName;

    /**
     * @var Collection<int, MenuItem>
     */
    #[ORM\OneToMany(targetEntity: MenuItem::class, mappedBy: 'menu', orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $items;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
        $this->items = new ArrayCollection();
    }

    /** @return MenuTranslation */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    public function setInternalName(string $internalName): static
    {
        $this->internalName = $internalName;

        return $this;
    }

    public function getName(): string
    {
        return $this->translate()->getName();
    }

    /**
     * @return Collection<int, MenuItem>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(MenuItem $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setMenu($this);
        }

        return $this;
    }

    public function removeItem(MenuItem $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getMenu() === $this) {
                $item->setMenu(null);
            }
        }

        return $this;
    }
}
