<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\Entity\Event;
use ADS\UCCIA\Entity\FrequentlyAskedQuestion;
use ADS\UCCIA\Entity\Page;
use ADS\UCCIA\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
#[AdminDashboard(routePath: '/%app.security.admin_prefix%', routeName: 'uccia_admin')]
final class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureAssets(): Assets
    {
        return parent::configureAssets()
            ->addAssetMapperEntry(Asset::new('admin'));
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('UCCIA Plateform')
            ->generateRelativeUrls();
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setDateFormat('medium')
            ->setPaginatorPageSize(20)
            ->setPaginatorRangeSize(4)
            // ->setPaginatorUseOutputWalkers(true)
            // ->setPaginatorFetchJoinCollection(true)
            ->setDateTimeFormat(DateTimeField::FORMAT_MEDIUM, DateTimeField::FORMAT_SHORT);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Gestion de contenu');
        yield MenuItem::linkToCrud('Pages', 'fa fa-file-text', Page::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Post::class);
        yield MenuItem::linkToCrud('Événements', 'fa fa-calendar', Event::class);
        yield MenuItem::linkToCrud('FAQ', 'fa fa-question', FrequentlyAskedQuestion::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->addMenuItems([
                MenuItem::linkToUrl('Voir le site', 'fa fa-globe', '#')->setLinkTarget('_blank'), // $this->generateUrl('app_dashboard')
                // MenuItem::section(),
                // MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
            ]);
    }
}
