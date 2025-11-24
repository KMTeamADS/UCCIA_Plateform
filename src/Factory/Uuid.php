<?php

declare(strict_types=1);

namespace ADS\UCCIA\Factory;

final class Uuid extends \Symfony\Component\Uid\Uuid
{
    public static function tryFromString(string $uuid): ?Uuid
    {
        try {
            return self::fromString($uuid);
        } catch (\Exception) {
            return null;
        }
    }
}
