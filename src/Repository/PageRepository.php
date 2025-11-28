<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /** @return Page[] */
    public function findEnabledSequence(array $slugs = [], string $locale = 'fr'): array
    {
        $queryBuilder = $this->createTranslationBasedQueryBuilder($locale)
            ->addSelect('page')
            ->andWhere('page.enabled = 1');

        if (count($slugs) === 1) {
            $queryBuilder->setMaxResults(1);
        }

        /** @var Page[] $results */
        $results = $queryBuilder
            ->andWhere($queryBuilder->expr()->in('translation.slug', $slugs))
            ->getQuery()
            ->getResult();

        if (count($results) === 0) {
            return $results;
        }

        $resultsSortedBySlug = [];
        foreach ($results as $page) {
            $resultsSortedBySlug[$page->getSlug()] = $page;
        }

        $pages = $resultsSortedBySlug;

        if (count($slugs) > 0) {
            $pages = [];
            foreach ($slugs as $value) {
                if (!array_key_exists($value, $resultsSortedBySlug)) {
                    // Means at least one page in the tree is not enabled
                    return [];
                }

                $pages[$value] = $resultsSortedBySlug[$value];
            }
        }

        return $pages;
    }

    protected function createTranslationBasedQueryBuilder(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('page')
            ->addSelect('translations')
            ->leftJoin('page.translations', 'translations')
            ->innerJoin('page.translations', 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale);
    }
}
