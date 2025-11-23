<?php

declare(strict_types = 1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\Enums\PageType;
use ADS\UCCIA\Entity\Page;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

final class PageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une nouvelle page')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de la page <small>(#%entity_short_id%)</small>');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('title', 'Titre'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter une page')->setIcon('fa fa-plus'))
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('name', 'Nom de la page')->setRequired(true)->setColumns(12)
            )
            ->addTranslatableField(
                TextField::new('title', 'Titre de la page')->setRequired(true)->setColumns(12)
            )
            ->addTranslatableField(
                SlugField::new('slug')
                    ->setTargetFieldName('title')
                    ->setRequired(true)
                    ->setHelp('URL de la page, doit être unique')
                    ->setColumns(12)
            )
            ->addTranslatableField(
                TextEditorField::new('content', 'Contenu')->setNumOfRows(6)->setColumns(12)
            );

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom de la page')->hideOnForm();
        yield ChoiceField::new('type', 'Type de page')
            ->setFormTypeOption('choice_label', fn (PageType $value) => $value->label())
            ->formatValue(fn (PageType $value) => $value->label());
        yield BooleanField::new('enabled', 'Activé ?')->setFormTypeOption('label_attr.class', 'checkbox-switch');
        yield AssociationField::new('parent', 'Page parent')
            ->autocomplete()
            ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                $queryBuilder->andWhere('entity.enabled = 1');
            })
            ->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
