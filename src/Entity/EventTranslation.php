<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\EventTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_event_translations')]
#[ORM\Entity(repositoryClass: EventTranslationRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_LOCALE_SLUG', columns: ['locale', 'slug'])]
#[UniqueEntity(['locale', 'slug'], errorPath: 'slug')]
class EventTranslation implements TranslationInterface
{
    use WithUuid;
    use TranslationTrait;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    #[ORM\Column(length: 255)]
    private string $title;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    #[Assert\Regex(pattern: '/^[a-z0-9\-]+$/', htmlPattern: '[a-z0-9\-]+')]
    #[ORM\Column(length: 255)]
    private string $slug;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }
}
