<?php

declare(strict_types=1);

namespace ADS\UCCIA\Resolver;

use ADS\UCCIA\Entity\Page;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageHierarchyResolver
{
    /**
     * Tries to identify the current active page based on the fetched pages and url slugs.
     */
    public function resolve(array $pages, array $slugsArray): Page
    {
        // Si le nombre de pages trouvées correspond au nombre de slugs,
        // on doit vérifier que la hiérarchie (Parent > Enfant) est respectée.
        if (count($pages) === count($slugsArray)) {
            return $this->validatePathIntegrity($slugsArray, $pages);
        }

        $page = current($pages);

        if (!$page instanceof Page) {
            throw new NotFoundHttpException('No page found.');
        }

        return $page;
    }

    /**
     * Validates that the pages follow a strict Parent -> Child relationship
     * matching the order of the slugs.
     */
    private function validatePathIntegrity(array $slugs, array $pagesByKey): Page
    {
        // 1. Vérification rapide : A-t-on toutes les pages demandées ?
        // On compare les clés (slugs des pages) avec les slugs de l'URL.
        $foundSlugs = array_keys($pagesByKey);
        $sortedSlugs = $slugs;
        sort($sortedSlugs);
        sort($foundSlugs);

        if ($sortedSlugs !== $foundSlugs || !count($slugs) || count($slugs) !== count($pagesByKey)) {
            throw new NotFoundHttpException('Page count or slug mismatch.');
        }

        $currentElement = null;
        $previousElement = null;

        foreach ($slugs as $slug) {
            $currentElement = $pagesByKey[$slug] ?? null;

            if (!$currentElement) {
                // Ne devrait pas arriver grâce au check précédent, mais sécurité
                throw new NotFoundHttpException(sprintf('Page "%s" not found in dataset.', $slug));
            }

            // Si on a un élément précédent, on doit vérifier que l'actuel est bien son enfant
            if ($previousElement) {
                $parent = $currentElement->getParent();

                // Si la page actuelle n'a pas de parent OU si son parent n'est pas l'élément précédent
                if (!$parent || $parent->getSlug() !== $previousElement->getSlug()) {
                    throw new NotFoundHttpException(sprintf(
                        'Hierarchy broken: "%s" is not the parent of "%s".',
                        $previousElement->getSlug(),
                        $slug,
                    ));
                }
            }

            $previousElement = $currentElement;
        }

        // On retourne le dernier élément validé (la page cible)
        return $currentElement;
    }
}
