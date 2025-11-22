<?php

declare(strict_types=1);

namespace ADS\UCCIA\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait WithUuid
{
    #[ORM\Id]
    // #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid', unique: true)]
    private Uuid $id;

    public function getId(): Uuid
    {
        return $this->id;
    }
}
