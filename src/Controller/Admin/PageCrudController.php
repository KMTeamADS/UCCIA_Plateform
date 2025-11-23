<?php

declare(strict_types = 1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\EasyAdmin\Filter\TranslatableTextFilter;
use ADS\UCCIA\Entity\Enums\PageType;
use ADS\UCCIA\Entity\Page;
use ADS\UCCIA\Repository\PageRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\ActionGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

final class PageCrudController extends AbstractCrudController
{
    use WithRequest;

    public function __construct(
        private readonly PageRepository $pageRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function createEntity(string $entityFqcn): Page
    {
        $crudPage = new Page();

        $crudPageId = $this->getRequest()->query->get('crudPageId');
        $crudPageType = $this->getRequest()->query->get('crudPageType');

        if (null !== $crudPageType) {
            $crudPage->setType(PageType::tryFrom($crudPageType) ?? PageType::STANDARD);
        }

        if ($crudPageId !== null) {
            $crudPageUuid = Uuid::fromString($crudPageId);
            $parent = $this->pageRepository->find($crudPageUuid);
            $crudPage->setParent($parent);
        }

        return $crudPage;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Page')
            ->setEntityLabelInPlural('Pages')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setPageTitle(Crud::PAGE_NEW, function () {
                $crudPageType = $this->getRequest()->query->get('crudPageType', '');
                $pageType = PageType::tryFrom($crudPageType);

                if ($pageType !== null) {
                    return \sprintf('Ajouter une nouvelle <small>(%s)</small>', $pageType->label());
                }

                return 'Ajouter une nouvelle page';
            })
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de la page <small>(#%entity_short_id%)</small>');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TranslatableTextFilter::new('title', 'Titre'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $createActions = ActionGroup::new('create')
            ->createAsGlobalActionGroup()
            ->setIcon('fa fa-plus')
            ->asPrimaryActionGroup();

        foreach (PageType::cases() as $case) {
            $action = Action::new('create_' . $case->value, 'Nouvelle ' . strtolower($case->label()))
                ->linkToUrl(function () use ($case) {
                    return $this->adminUrlGenerator
                        ->setController(self::class)
                        ->setAction(Action::NEW)
                        ->set('crudPageType', $case->value)
                        ->generateUrl();
                });

            if ($case === PageType::STANDARD) {
                $createActions->addMainAction($action);
                continue;
            }

            $createActions->addAction($action);
        }

        return $actions
            ->add(Crud::PAGE_INDEX, $createActions)
            ->add(
                Crud::PAGE_INDEX,
                Action::new('children', 'Pages enfants', 'fa fa-list-ul')
                    ->setCssClass('action-children')
                    ->setHtmlAttributes(['title' => 'Pages enfants'])
                    ->linkToUrl(function (Page $page) {
                        return $this->adminUrlGenerator
                            ->setController(self::class)
                            ->setAction(Action::INDEX)
                            ->set('crudPageId', $page->getId())
                            ->unset('entityId')
                            ->generateUrl();
                    })
            )
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
                $request = Request::createFromGlobals();
                $crudPageType = $request->query->get('crudPageType', '');
                $pageType = PageType::tryFrom($crudPageType);
                $label = 'Enregistrer et ajouter une nouvelle page';

                if ($pageType !== null) {
                    $label = \sprintf('Enregistrer et ajouter une nouvelle %s', $pageType->label());
                }

                return $action->setLabel($label);
            })
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $crudPageType = $this->getRequest()->query->get('crudPageType');
        $pageType = PageType::tryFrom($crudPageType ?? '') ?? PageType::STANDARD;

        $translationsField = TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('name', 'Nom de la page')->setRequired(true)->setColumns(12),
            )
            ->addTranslatableField(
                TextField::new('title', 'Titre de la page')->setRequired(true)->setColumns(12),
            )
            ->addTranslatableField(
                SlugField::new('slug')
                    ->setTargetFieldName('title')
                    ->setRequired(true)
                    ->setHelp('URL de la page, doit être unique')
                    ->setColumns(12),
            )
            ->addTranslatableField(
                HiddenField::new('content'),
            );

        if ($pageType !== PageType::KNOT) {
            $translationsField->addTranslatableField(
                TextEditorField::new('content', 'Contenu')->setNumOfRows(6)->setColumns(12)
            );
        }

        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield $translationsField;

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom de la page')->hideOnForm();

        if ($crudPageType === null && $pageName !== Crud::PAGE_EDIT) {
            yield ChoiceField::new('type', 'Type de page')
                ->setFormTypeOption('choice_label', fn(PageType $value) => $value->label())
                ->formatValue(fn(PageType $value) => $value->label());
        }

        yield BooleanField::new('enabled', 'Activé ?')->setFormTypeOption('label_attr.class', 'checkbox-switch');

        if ($pageType !== PageType::KNOT) {
            yield AssociationField::new('parent', 'Page parent')
                ->autocomplete()
                ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                    $queryBuilder->andWhere('entity.enabled = 1');
                })
                ->hideOnIndex();
        }

        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $crudPageId = $this->getRequest()->query->get('crudPageId');

        if ($crudPageId !== null) {
            try {
                $crudPageUuid = Uuid::fromString($crudPageId);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq('entity.parent', ':crudPageUuid'))
                    ->setParameter(':crudPageUuid', $crudPageUuid->toBinary());
            } catch (InvalidArgumentException) {}
        }

        return $queryBuilder;
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'] ?? null;

        $url = match ($submitButtonName) {
            Action::SAVE_AND_CONTINUE => $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::EDIT)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl(),
            Action::SAVE_AND_RETURN => $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::INDEX)
                ->unset('crudPageType')
                ->generateUrl(),
            Action::SAVE_AND_ADD_ANOTHER => $this->container->get(AdminUrlGenerator::class)->setAction(Action::NEW)->generateUrl(),
            default => $this->generateUrl($context->getDashboardRouteName()),
        };

        return $this->redirect($url);
    }
}
