<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Query\Expr;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    use WithCreateTranslationBasedQueryBuilder;

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

    public function findByInternalName(string $internalName, string $locale = 'fr'): ?Menu
    {
        return $this->createTranslationBasedQueryBuilder($locale, 'menu')
            ->addSelect(['menu', 'items', 'page', 'page_child'])
            ->leftJoin('menu.items', 'items', Expr\Join::WITH, 'items.enabled = 1')
            ->leftJoin('items.page', 'page', Expr\Join::WITH, 'page.enabled = 1')
            ->leftJoin('page.children', 'page_child', Expr\Join::WITH, 'page_child.enabled = 1')
            ->andWhere('menu.enabled = 1')
            ->andWhere('menu.internalName = :internalName')
            ->setParameter('internalName', $internalName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
