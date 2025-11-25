<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('app/home.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}
