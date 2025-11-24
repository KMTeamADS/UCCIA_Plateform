<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Enums\MenuItemType;
use ADS\UCCIA\Entity\Traits\WithEnable;
use ADS\UCCIA\Entity\Traits\WithTimestamps;
use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\MenuItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_menu_items')]
#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem implements TranslatableInterface
{
    use WithUuid;
    use TranslatableTrait;
    use WithTimestamps;
    use WithEnable;

    #[Assert\NotBlank]
    #[ORM\Column(length: 50, enumType: MenuItemType::class, options: ['default' => MenuItemType::PAGE])]
    private MenuItemType $type = MenuItemType::PAGE;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private bool $newWindow = false;

    #[ORM\Column(type: Types::INTEGER, options: ['default' => 0])]
    private int $position = 0;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(targetEntity: Page::class)]
    private ?Page $page = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString()
    {
        if ($this->isPage()) {
            return $this->getPage()?->getName();
        }

        return $this->getName();
    }

    /** @return MenuItemTranslation */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    public function getName(): string
    {
        return $this->translate()->getName();
    }

    public function getUrl(): string
    {
        return $this->translate()->getUrl();
    }

    public function getType(): MenuItemType
    {
        return $this->type;
    }

    public function setType(MenuItemType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isPage(): bool
    {
        return $this->getType() === MenuItemType::PAGE && null !== $this->getPage();
    }

    public function isUrl(): bool
    {
        return $this->getType() === MenuItemType::URL && null !== $this->getUrl();
    }

    public function isNewWindow(): bool
    {
        return $this->newWindow;
    }

    public function setNewWindow(bool $newWindow): static
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getMenu(): Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(?Page $page): static
    {
        $this->page = $page;

        return $this;
    }
}
