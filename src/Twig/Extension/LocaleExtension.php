<?php

declare(strict_types=1);

namespace ADS\UCCIA\Twig\Extension;

use ADS\UCCIA\Twig\Runtime\LocaleExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', [LocaleExtensionRuntime::class, 'getLocales']),
            new TwigFunction('is_rtl', [LocaleExtensionRuntime::class, 'isRtl']),
        ];
    }
}
