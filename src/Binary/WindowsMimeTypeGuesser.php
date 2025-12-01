<?php

declare(strict_types=1);

namespace ADS\UCCIA\Binary;

use JoliCode\MediaBundle\Binary\MimeTypeGuesser;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\Mime\MimeTypeGuesserInterface;
use Symfony\Component\Mime\MimeTypesInterface;

#[When(env: 'dev')]
#[AsDecorator('joli_media.mime_type_guesser')]
final class WindowsMimeTypeGuesser extends MimeTypeGuesser
{
    public function __construct(
        private readonly MimeTypesInterface $mimeTypes,
        private readonly MimeTypeGuesserInterface $mimeTypeGuesser,
    ) {
        parent::__construct($this->mimeTypes, $this->mimeTypeGuesser);
    }

    public function getPossibleExtension(string $mimeType): string
    {
        $possibleExtensions = $this->mimeTypes->getExtensions($mimeType);

        return $possibleExtensions[0] ?? $mimeType;
    }

    public function guessMimeTypeFromContent(string $content): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'media');
        file_put_contents($temporaryFile, $content);
        //$mimeType = $this->mimeTypeGuesser->guessMimeType($temporaryFile);
        $mimeType = $this->mimeTypes->guessMimeType($temporaryFile);
        unlink($temporaryFile);

        return $mimeType ?? 'application/octet-stream';
    }
}
