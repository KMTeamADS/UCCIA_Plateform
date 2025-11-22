<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Traits\WithEnable;
use ADS\UCCIA\Entity\Traits\WithTimestamps;
use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\FrequentlyAskedQuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_frequently_asked_questions')]
#[ORM\Entity(repositoryClass: FrequentlyAskedQuestionRepository::class)]
class FrequentlyAskedQuestion implements TranslatableInterface
{
    use WithUuid;
    use TranslatableTrait;
    use WithTimestamps;
    use WithEnable;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true, 'default' => 0])]
    private int $position = 0;

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->createdAt = new \DateTimeImmutable();
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

    /** @return FrequentlyAskedQuestionTranslation */
    public function translate(?string $locale = null, bool $fallbackToDefault = true): TranslationInterface
    {
        return $this->doTranslate($locale, $fallbackToDefault);
    }

    public function getQuestion(): string
    {
        return $this->translate()->getQuestion();
    }
}
