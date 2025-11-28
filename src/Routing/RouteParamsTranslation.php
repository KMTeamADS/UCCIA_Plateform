<?php

declare (strict_types = 1);

namespace ADS\UCCIA\Routing;

use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;

#[AsAlias(RouteParamsTranslationInterface::class)]
final readonly class RouteParamsTranslation implements RouteParamsTranslationInterface
{
    public function __construct(
        #[AutowireLocator(RouteParamsTranslationInterface::class, defaultIndexMethod: 'supports')]
        private ServiceLocator $translations,
    ) {
    }

    #[\Override] public function translate(string $routeName, array $currentParams, string $targetLocale): array
    {
        if (!$this->translations->has($routeName)) {
            return array_merge($currentParams, ['_locale' => $targetLocale]);
        }

        /** @var RouteParamsTranslationInterface $translation */
        $translation = $this->translations->get($routeName);

        return $translation->translate($routeName, $currentParams, $targetLocale);
    }
}
