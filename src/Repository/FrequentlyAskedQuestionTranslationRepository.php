<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\FrequentlyAskedQuestionTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FrequentlyAskedQuestionTranslation>
 */
class FrequentlyAskedQuestionTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FrequentlyAskedQuestionTranslation::class);
    }
}
