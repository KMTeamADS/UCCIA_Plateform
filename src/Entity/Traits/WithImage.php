<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JoliCode\MediaBundle\Doctrine\Types as MediaTypes;
use JoliCode\MediaBundle\Model\Media;
use JoliCode\MediaBundle\Validator\Media as MediaConstraint;

trait WithImage
{
    #[ORM\Column(type: MediaTypes::MEDIA, nullable: true)]
    #[MediaConstraint(
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        extensionMessage: 'Les extensions autorisées sont: {{ extensions }}.',
        allowedMimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        mimeTypeMessage: 'Les types MIME autorisés sont: {{ mimeTypes }}.',
        // allowedPaths: ['illustration', 'avatar'],
        // pathMessage: 'The file path "{{ value }}" is not allowed. Allowed paths must start with one of the following: {{ paths }}.',
        allowedTypes: ['image'],
        typeMessage: 'Les types autorisés sont: {{ types }}.',
        // maxPathLength: 255,
        // maxPathLengthMessage: 'The file path "{{ value }}" exceeds the maximum length of {{ limit }} characters.',
    )]
    private ?Media $image = null;

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): static
    {
        $this->image = $image;

        return $this;
    }
}
