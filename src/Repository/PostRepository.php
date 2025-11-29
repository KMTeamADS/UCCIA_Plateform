<?php

declare(strict_types=1);

namespace ADS\UCCIA\Repository;

use ADS\UCCIA\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    use WithCreateTranslationBasedQueryBuilder;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPublished(string $slug, string $locale = 'fr'): ?Post
    {
        return $this->createTranslationBasedQueryBuilder($locale, 'post')
            ->addSelect('post')
            ->andWhere('translation.slug = :slug')
            ->andWhere('post.publishedAt IS NOT NULL')
            ->andWhere('post.publishedAt <= :now')
            ->setParameter('slug', $slug)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
