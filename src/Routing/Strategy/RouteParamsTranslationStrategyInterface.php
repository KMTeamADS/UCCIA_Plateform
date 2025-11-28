<?php

declare(strict_types=1);

namespace ADS\UCCIA\Routing\Strategy;

use ADS\UCCIA\Routing\RouteParamsTranslationInterface;

interface RouteParamsTranslationStrategyInterface extends RouteParamsTranslationInterface
{
    public static function supports(): string;
}
