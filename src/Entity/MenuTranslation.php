<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\MenuTranslationRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_menu_translations')]
#[ORM\Entity(repositoryClass: MenuTranslationRepository::class)]
class MenuTranslation implements TranslationInterface
{
    use WithUuid;
    use TranslationTrait;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    #[ORM\Column(length: 255)]
    private string $name;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
