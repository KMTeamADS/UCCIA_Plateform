<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final class MenuCrudController extends AbstractCrudController
{
    public function __construct(private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')

            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des menus')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajout d\'un menu')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification du menu <small>(#%entity_short_id%)</small>');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('name', 'Nom du menu'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(
                Crud::PAGE_INDEX,
                Action::new('addmenuitem', 'Éléments du menu', 'fa fa-list-ul')
                    ->setCssClass('action-addmenuitem')
                    ->setHtmlAttributes(['title' => 'Éléments du menu'])
                    ->linkToUrl(function (Menu $menu) {
                        return $this->adminUrlGenerator
                            ->setController(MenuItemCrudController::class)
                            ->setAction(Action::INDEX)
                            ->set('crudMenuId', $menu->getId())
                            ->unset('entityId')
                            ->generateUrl();
                    })
            )
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter un menu')->setIcon('fa fa-plus'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                return $action->setLabel('Enregistrer et ajouter un nouveau menu');
            })
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield TextField::new('internalName', 'Nom d\'identification')
            ->setHelp('Utiliser pour identifier le menu depuis le front. Doit être unique');

        if ($pageName === Crud::PAGE_EDIT) {
            yield TextField::new('internalName', 'Nom d\'identification')
                ->setFormTypeOption('disabled', true)
                ->setHelp('Utiliser pour identifier le menu depuis le front. Doit être unique');
        }

        yield TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('name', 'Nom du menu')->setRequired(true)->setColumns(12)
            );

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom du menu')->hideOnForm();
        yield BooleanField::new('enabled', 'Activé ?')->setFormTypeOption('label_attr.class', 'checkbox-switch');
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
