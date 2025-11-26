<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AdminSecurityController extends AbstractController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    #[Route(path: '/%app.security.admin_prefix%/login', name: 'admin_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // if user is already logged in, don't display the login page again
        if ($security->getUser()) {
            return new RedirectResponse($this->adminUrlGenerator->generateUrl());
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            // 'page_title' => '<img src="/logo.svg" alt="UCCIA Plateform" />',
            // 'page_title' => 'UCCIA Plateform',
            'csrf_token_intention' => 'authenticate',
            'target_path' => $this->adminUrlGenerator->generateUrl(),
            'username_label' => 'Email',
            'password_label' => 'Mot de passe',
            'sign_in_label' => 'Connexion',
            'remember_me_enabled' => true,
            'remember_me_label' => 'Se souvenir de moi',
        ]);
    }

    #[Route(path: '/%app.security.admin_prefix%/logout', name: 'admin_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
