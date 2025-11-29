<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use Doctrine\ORM\QueryBuilder;

/** @method QueryBuilder createQueryBuilder(string $alias, null|string $indexBy = null) */
trait WithCreateTranslationBasedQueryBuilder
{
    protected function createTranslationBasedQueryBuilder(string $locale, string $alias = 'entity'): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->addSelect('translations')
            ->leftJoin("$alias.translations", 'translations')
            ->innerJoin("$alias.translations", 'translation')
            ->andWhere('translation.locale = :locale')
            ->setParameter('locale', $locale);
    }
}
