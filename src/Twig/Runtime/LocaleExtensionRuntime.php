<?php

declare(strict_types=1);

namespace ADS\UCCIA\Twig\Runtime;

use Symfony\Component\Intl\Locales;
use Twig\Extension\RuntimeExtensionInterface;

final class LocaleExtensionRuntime implements RuntimeExtensionInterface
{
    /**
     * @var list<array{code: string, name: string}>|null
     */
    private ?array $locales = null;

    public function __construct(
        /** @var string[] */
        private readonly array $enabledLocales,
        private readonly string $defaultLocale,
    ) {
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getLocales(): array
    {
        if (null !== $this->locales) {
            return $this->locales;
        }

        $this->locales = [];

        foreach ($this->enabledLocales as $localeCode) {
            $this->locales[] = ['code' => $localeCode, 'name' => Locales::getName($localeCode, $localeCode)];
        }

        return $this->locales;
    }

    public function isRtl(?string $locale = null): bool
    {
        $locale = $locale ?? $this->defaultLocale;

        return \in_array($locale, ['ar', 'fa', 'he', 'ur', 'ps', 'sd'], true);
    }
}
