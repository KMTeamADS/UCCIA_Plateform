<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\HttpFoundation\Request;

/** @method AdminContext|null getContext() */
trait WithRequest
{
    private function getRequest(): Request
    {
        $request = $this->getContext()?->getRequest();

        if (!$request instanceof Request) {
            throw new \LogicException('Request must be set.');
        }

        return $request;
    }
}
