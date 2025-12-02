<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity\Enums;

enum OriginType: string
{
    case CCIA_NGAZIDJA = 'ccia_ngazidja';

    case CCIA_MWALI = 'ccia_mwali';

    case CCIA_NDZUANI = 'ccia_ndzuani';

    public function label(): string {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string {
        return match ($value) {
            self::CCIA_NGAZIDJA => 'CCIA de Ngazidja',
            self::CCIA_MWALI => 'CCIA de Mwali',
            self::CCIA_NDZUANI => 'CCIA de Ndzuani',
        };
    }
}
