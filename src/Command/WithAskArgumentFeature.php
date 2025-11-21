<?php

declare(strict_types=1);

namespace ADS\UCCIA\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/** @property SymfonyStyle $io */
trait WithAskArgumentFeature
{
    private function askArgument(InputInterface $input, string $name, bool $hidden = false): void
    {
        $value = (string) $input->getArgument($name);

        if ($value !== '') {
            $this->io->text(\sprintf(' > <info>%s</info>: %s', $name, $value));
        } else {
            $value = match ($hidden) {
                false => $this->io->ask(\strtoupper($name)),
                default => $this->io->askHidden(\strtoupper($name))
            };
            $input->setArgument($name, $value);
        }
    }
}
