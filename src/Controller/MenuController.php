<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller;

use ADS\UCCIA\Repository\MenuRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsController]
final readonly class MenuController
{
    public function __construct(
        private MenuRepository $menuRepository,
        private Environment $twig,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(string $name, string $template, Request $request): Response
    {
        $menu = $this->menuRepository->findByInternalName($name, $request->getLocale());

        return new Response($this->twig->render($template, ['menu' => $menu]));
    }
}
