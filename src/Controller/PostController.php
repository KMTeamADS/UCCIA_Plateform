<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller;

use ADS\UCCIA\Entity\Post;
use ADS\UCCIA\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/articles', name: 'app_post_')]
final class PostController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/{slug}', name: 'show', requirements: ['slug' => '[a-z0-9\-]*'], methods: ['GET'])]
    public function show(string $slug, Request $request): Response
    {
        $post = $this->postRepository->findPublished($slug, $request->getLocale());

        if (!$post instanceof Post) {
            throw $this->createNotFoundException('Post not found');
        }

        return $this->render('app/post/show.html.twig', [
            'controller_name' => 'PostController',
            'post' => $post,
        ]);
    }
}
