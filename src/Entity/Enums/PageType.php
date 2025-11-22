<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity\Enums;

enum PageType: string
{
    case STANDARD = 'standard';

    case EVENT = 'event';

    case POST = 'post';

    case FAQ = 'faq';

    case KNOT = 'knot';

    case CONTACT = 'contact';

    public function label(): string {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string {
        return match ($value) {
            self::STANDARD => 'Page standard',
            self::EVENT => 'Page événements',
            self::POST => 'Page Articles',
            self::FAQ => 'Page FAQ',
            self::KNOT => 'Page Nœud',
            self::CONTACT => 'Page Contact',
        };
    }
}
