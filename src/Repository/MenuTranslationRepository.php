<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\MenuTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MenuTranslation>
 */
class MenuTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuTranslation::class);
    }
}
