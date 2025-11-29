<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller;

use ADS\UCCIA\Entity\Enums\PageType;
use ADS\UCCIA\Repository\PageRepository;
use ADS\UCCIA\Resolver\PageHierarchyResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly PageHierarchyResolver $pageHierarchyResolver,
    ) {
    }

    #[Route('/{url}', name: 'app_page_show', requirements: ['url' => '[a-z0-9\-_\/]*'], methods: ['GET'], priority: -1)]
    public function show(string $url, Request $request): Response
    {
        if (str_ends_with($url, '/')) {
            return $this->redirectToRoute('app_page_show', ['url' => rtrim($url, '/')]);
        }

        $slugsArray = preg_split('~/~', $url, -1, PREG_SPLIT_NO_EMPTY);
        $pages = $this->pageRepository->findEnabledSequence($slugsArray, $request->getLocale());

        if (!count($pages) || (count($slugsArray) && count($pages) !== count($slugsArray))) {
            throw $this->createNotFoundException(
                count($slugsArray)
                    ? 'Page not found'
                    : 'No page has been configured for this url. Please check your existing pages.',
            );
        }

        $currentPage = $this->pageHierarchyResolver->resolve($pages, $slugsArray);
        $viewContext = \sprintf('app/page/%s.html.twig', $currentPage->getType()->value);
        $viewParams = [
            'page' => $currentPage,
            'pages' => $pages,
        ];

        if ($currentPage->getType() === PageType::POST) {
            $viewParams['posts'] = [];
        }

        return $this->render($viewContext, $viewParams);
    }
}
