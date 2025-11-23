<?php

declare (strict_types = 1);

namespace ADS\UCCIA\Entity\Enums;

enum MenuItemType: string
{
    case PAGE = 'page';

    case URL = 'url';

    public function label(): string {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string {
        return match ($value) {
            self::PAGE => 'Page',
            self::URL => 'URL',
        };
    }
}
