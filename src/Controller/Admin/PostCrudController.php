<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
// use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
// use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use JoliCode\MediaBundle\Bridge\EasyAdmin\Field\MediaChoiceField;
// use Symfony\Component\Asset\PathPackage;
// use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;

final class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

//    public function configureAssets(Assets $assets): Assets
//    {
//        // this should not be needed, but there is a bug in EA with assets in nested forms
//        // see https://github.com/EasyCorp/EasyAdminBundle/issues/6127
//        $package = new PathPackage(
//            '/bundles/jolimediaeasyadmin',
//            new JsonManifestVersionStrategy(__DIR__ . '/../../../public/bundles/jolimediaeasyadmin/manifest.json'),
//        );
//
//        return $assets
//            ->addCssFile($package->getUrl('joli-media-easy-admin.css'))
//            ->addJsFile($package->getUrl('joli-media-easy-admin.js'));
//    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Article')
            ->setEntityLabelInPlural('Articles')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un nouvel article')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de l\'article <small>(#%entity_short_id%)</small>')
            ->addFormTheme('@JoliMediaEasyAdmin/form/form_theme.html.twig');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('title', 'Titre'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setLabel('Ajouter un article')->setIcon('fa fa-plus'))
            ->disable(Action::DETAIL);
            // ->setPermission(Action::DETAIL, 'ROLE_UNAVAILABLE');
            // ->add(Crud::PAGE_INDEX, Action::DETAIL)
            // ->update(Crud::PAGE_INDEX, Action::DETAIL, fn (Action $action) => $action->setIcon('fa fa-eye'));
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
                    ->setHelp('URL de l\'article, doit être unique')
                    ->setColumns(12)
            )
            ->addTranslatableField(
                TextEditorField::new('content', 'Contenu')->setNumOfRows(6)->setColumns(12)
            );

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield MediaChoiceField::new('image')
            ->setRequired(false)
            ->hideOnForm();
        yield TextField::new('title', 'Titre')->hideOnForm();
        yield DateTimeField::new('publishedAt', 'Date de publication')->setFormTypeOptions([
            'attr' => ['class' => 'w-100'],
        ]);
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
        // yield CollectionField::new('translations', null)->onlyOnDetail();
        yield MediaChoiceField::new('image')
            ->setRequired(false)
            ->onlyOnForms();
    }
}
