<?php

declare(strict_types=1);

namespace ADS\UCCIA\Routing;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag] // 'app.route_translation_strategy'
interface RouteParamsTranslationInterface
{
    public function translate(string $routeName, array $currentParams, string $targetLocale): array;
}
