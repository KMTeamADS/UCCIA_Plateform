<?php

declare(strict_types = 1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\FrequentlyAskedQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FrequentlyAskedQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return FrequentlyAskedQuestion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('FAQ')
            ->setEntityLabelInPlural('FAQs')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des questions fréquentes')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une nouvelle question')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de la question <small>(#%entity_short_id%)</small>');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('question'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter une question')->setIcon('fa fa-plus'))
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('question')->setRequired(true)->setColumns(12)
            )
            ->addTranslatableField(
                TextEditorField::new('answer', 'Réponse')->setNumOfRows(6)->setColumns(12)
            );

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('question')->hideOnForm();
        yield IntegerField::new('position');
        yield BooleanField::new('enabled', 'Activé ?');
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }
}
