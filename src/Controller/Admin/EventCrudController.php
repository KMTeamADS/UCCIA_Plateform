<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Événement')
            ->setEntityLabelInPlural('Événements')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un nouvel Événement')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de l\'événement <small>(#%entity_short_id%)</small>');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('title', 'Titre'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter un événement')->setIcon('fa fa-plus'))
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('title', 'Titre')->setRequired(true)->setColumns(12)
            )
            ->addTranslatableField(
                SlugField::new('slug')
                    ->setTargetFieldName('title')
                    ->setRequired(true)
                    ->setHelp('URL de l\'événement, doit être unique')
                    ->setColumns(12)
            )
            ->addTranslatableField(
                TextEditorField::new('description')->setNumOfRows(6)->setColumns(12)
            );

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('title', 'Titre')->hideOnForm();
        yield DateTimeField::new('startAt', 'Date de début')->setFormTypeOptions([
            'attr' => ['class' => 'w-100'],
        ]);
        yield DateTimeField::new('endAt', 'Date de fin')->setFormTypeOptions([
            'attr' => ['class' => 'w-100'],
        ]);
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
