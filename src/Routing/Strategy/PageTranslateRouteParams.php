<?php

declare(strict_types=1);

namespace ADS\UCCIA\Routing\Strategy;

use ADS\UCCIA\Entity\Page;
use ADS\UCCIA\Repository\PageRepository;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class PageTranslateRouteParams implements RouteParamsTranslationStrategyInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private PageRepository $pageRepository,
    ) {
    }

    #[\Override] public function translate(string $routeName, array $currentParams, string $targetLocale): array
    {
        // Récupérer l'URL brute actuelle (ex : 'ma-super-page/enfant')
        $currentUrl = $currentParams['url'] ?? '';

        // Récupérer la locale actuelle de la requête
        $currentRequestLocale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'fr';

        // Retrouver l'entité Page correspondante en base de données
        $slugsArray = preg_split('~/~', $currentUrl, -1, PREG_SPLIT_NO_EMPTY);

        // Attention : findEnabledSequence renvoie un tableau, on veut le dernier élément (la page courante)
        $pages = $this->pageRepository->findEnabledSequence($slugsArray, $currentRequestLocale);

        if (empty($pages)) {
            // Si la page n'est pas trouvée (ex : 404), on ne peut pas traduire l'URL.
            // On retourne l'URL actuelle ou vide (redirection accueil).
            return $currentParams;
        }

        // On prend la dernière page de la séquence (la page active).
        /** @var Page $currentPage */
        $currentPage = array_last($pages);
        $newUrl = $currentPage->getHierarchicalUrl($targetLocale);

        return ['url' => $newUrl];
    }

    #[\Override] public static function supports(): string
    {
        return 'app_page_show';
    }
}
