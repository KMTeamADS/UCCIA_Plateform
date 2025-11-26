<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity\Traits;

use ADS\UCCIA\Entity\PageTranslation;

/** @method  PageTranslation translate(?string $locale = null, bool $fallbackToDefault = true) */
trait WithPageTranslation
{
    public function getName(): string
    {
        return $this->translate()->getName();
    }

    public function getSlug(): string
    {
        return $this->translate()->getSlug();
    }
}
