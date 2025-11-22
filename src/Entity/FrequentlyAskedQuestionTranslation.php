<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity;

use ADS\UCCIA\Entity\Traits\WithUuid;
use ADS\UCCIA\Repository\FrequentlyAskedQuestionTranslationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslationInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslationTrait;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'app_frequently_asked_question_translations')]
#[ORM\Entity(repositoryClass: FrequentlyAskedQuestionTranslationRepository::class)]
class FrequentlyAskedQuestionTranslation implements TranslationInterface
{
    use WithUuid;
    use TranslationTrait;

    #[Assert\NotBlank]
    #[Assert\Length(['min' => 3, 'max' => 255])]
    #[ORM\Column(length: 255)]
    private string $question;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private string $answer;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }
}
