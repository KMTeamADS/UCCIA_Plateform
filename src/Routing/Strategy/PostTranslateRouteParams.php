<?php

declare(strict_types=1);

namespace ADS\UCCIA\Routing\Strategy;

use ADS\UCCIA\Routing\RouteParamsTranslationInterface;

final readonly class PostTranslateRouteParams implements RouteParamsTranslationStrategyInterface
{
    #[\Override] public function translate(string $routeName, array $currentParams, string $targetLocale): array
    {
        return array_merge($currentParams, ['_locale' => $targetLocale]);
    }

    #[\Override] public static function supports(): string
    {
        return 'app_post_show';
    }
}
