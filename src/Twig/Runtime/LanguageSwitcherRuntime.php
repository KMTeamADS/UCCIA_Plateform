<?php

declare(strict_types=1);

namespace ADS\UCCIA\Twig\Runtime;

use ADS\UCCIA\Routing\RouteParamsTranslationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class LanguageSwitcherRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private RouteParamsTranslationInterface $routeParamsTranslation,
    ) {
    }

    public function getLocalizedUrl(string $targetLocale): string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            return '#';
        }

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);

        // Par défaut, on change juste la locale
        $newParams = array_merge($routeParams, ['_locale' => $targetLocale]);

        // Si une stratégie supporte la route, elle prend le relais pour calculer les params (slug, url...)
        $translatedSpecificParams = $this->routeParamsTranslation->translate($route, $routeParams, $targetLocale);
        $newParams = array_merge($newParams, $translatedSpecificParams);

        // Nettoyage et Génération
        try {
            return $this->urlGenerator->generate($route, $newParams);
        } catch (\Exception) {
            // Fallback ultime : accueil
            return $this->urlGenerator->generate('app_home', ['_locale' => $targetLocale]);
        }
    }
}
