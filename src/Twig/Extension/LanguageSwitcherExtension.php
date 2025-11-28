<?php

declare(strict_types=1);

namespace ADS\UCCIA\Twig\Extension;

use ADS\UCCIA\Twig\Runtime\LanguageSwitcherRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LanguageSwitcherExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_localized_url', [LanguageSwitcherRuntime::class, 'getLocalizedUrl']),
        ];
    }
}
