<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findMenuWithoutAssociations(Uuid $menuId): ?Menu
    {
        $queryBuilder = $this->createQueryBuilder('menu');

        return $queryBuilder
            ->select(['menu'])
            ->where($queryBuilder->expr()->eq('menu.id', ':menuId'))
            ->setParameter('menuId', $menuId->toBinary())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
