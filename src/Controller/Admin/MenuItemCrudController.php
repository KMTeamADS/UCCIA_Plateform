<?php

declare(strict_types=1);

namespace ADS\UCCIA\Controller\Admin;

use ADS\UCCIA\EasyAdmin\Field\TranslationsField;
use ADS\UCCIA\Entity\Enums\MenuItemType;
use ADS\UCCIA\Entity\MenuItem;
use ADS\UCCIA\Factory\Uuid;
use ADS\UCCIA\Repository\MenuRepository;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\ActionGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MenuItemCrudController extends AbstractCrudController
{
    use WithRequest;

    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    public function index(AdminContext $context): Response | KeyValueStore
    {
        $crudMenuId = Uuid::tryFromString($this->getRequest()->query->get('crudMenuId', ''));

        if (null === $crudMenuId) {
            return $this->returnToCrudMenuIndexAction();
        }

        return parent::index($context);
    }

//    public function new(AdminContext $context): Response | KeyValueStore
//    {
//        $crudMenuId = Uuid::tryFromString($this->getRequest()->query->get('crudMenuId', ''));
//
//        if (null === $crudMenuId) {
//            return $this->returnToCrudMenuIndexAction();
//        }
//
//        return parent::new($context);
//    }

    public function edit(AdminContext $context): Response | KeyValueStore
    {
        $crudMenuId = Uuid::tryFromString($this->getRequest()->query->get('crudMenuId', ''));

        if (null === $crudMenuId) {
            return $this->returnToCrudMenuIndexAction();
        }

        return parent::edit($context);
    }

    public static function getEntityFqcn(): string
    {
        return MenuItem::class;
    }

    public function createEntity(string $entityFqcn): MenuItem
    {
        $crudMenu = $this->menuRepository->findMenuWithoutAssociations(
            Uuid::fromString($this->getRequest()->query->get('crudMenuId', '')),
        );
        $crudMenuItemType = $this->getRequest()->query->get('crudMenuItemType');

        $crudMenuItem = new MenuItem();

        if (null !== $crudMenu) {
            $crudMenuItem->setMenu($crudMenu);
        }

        if (null !== $crudMenuItemType) {
            $crudMenuItem->setType(MenuItemType::tryFrom($crudMenuItemType) ?? MenuItemType::PAGE);
        }

        return $crudMenuItem;
    }

    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        if (Crud::PAGE_INDEX === $responseParameters->get('pageName')) {
            $crudMenu = $this->menuRepository->findMenuWithoutAssociations(
                Uuid::fromString($this->getRequest()->query->get('crudMenuId', '')),
            );

            if (null !== $crudMenu) {
                $responseParameters->set('crudMenu', $crudMenu);
            }
        }

        return $responseParameters;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular('Élément du menu')
            ->setEntityLabelInPlural('Éléments du menu')

            ->setPageTitle(Crud::PAGE_NEW, 'Ajout d\'un élément du menu')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modification de l\'élément du menu <small>(#%entity_short_id%)</small>')
            ->overrideTemplate('crud/index', 'admin/menu_item/crud/index.html.twig');

            // ->overrideTemplate('crud/index', self::TEMPLATE_PATH . 'crud/index.html.twig')
            // ->overrideTemplate('crud/action', self::TEMPLATE_PATH . 'crud/action.html.twig')
    }

    public function configureActions(Actions $actions): Actions
    {
        $createActions = ActionGroup::new('create')
            ->createAsGlobalActionGroup()
            ->setIcon('fa fa-plus')
            ->asPrimaryActionGroup();

        foreach (MenuItemType::cases() as $case) {
            $action = Action::new('add_' . $case->value, 'Élément menu ' . strtolower($case->label()))
                ->linkToUrl(function () use ($case) {
                    $request = Request::createFromGlobals();
                    $crudMenuId = $request->query->get('crudMenuId', '');

                    return $this->adminUrlGenerator
                        ->setController(self::class)
                        ->setAction(Action::NEW)
                        ->set('crudMenuItemType', $case->value)
                        ->set('crudMenuId', $crudMenuId)
                        ->generateUrl();
                });

            if ($case === MenuItemType::PAGE) {
                $createActions->addMainAction($action);
                continue;
            }

            $createActions->addAction($action);
        }

        return parent::configureActions($actions)
            ->add(Crud::PAGE_INDEX, $createActions)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->linkToUrl(function (MenuItem $menuItem) {
                    return $this->adminUrlGenerator
                        ->setController(self::class)
                        ->setAction(Action::EDIT)
                        ->set('crudMenuItemType', $menuItem->getType()->value)
                        ->set('entityId', $menuItem->getId())
                        ->generateUrl();
                });
            })
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->disable(Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $crudMenuItemType = $this->getRequest()->query->get('crudMenuItemType');
        $menuItemType = MenuItemType::tryFrom($crudMenuItemType ?? '') ?? MenuItemType::PAGE;

        $translationsField = TranslationsField::new('translations')
            ->addTranslatableField(
                TextField::new('name', 'Nom')->setRequired(true)->setColumns(12),
            )
            ->addTranslatableField(
                HiddenField::new('url'),
            );

        if ($menuItemType !== MenuItemType::PAGE) {
            $translationsField->addTranslatableField(
                TextField::new('url', 'URL')->setColumns(12),
            );
        }

        yield FormField::addColumn(9)->addCssClass('border-end border-light-subtle');
        yield $translationsField;

        yield FormField::addColumn(3);
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom')->hideOnForm();

        if (($menuItemType === null && $pageName !== Crud::PAGE_EDIT) || $pageName === Crud::PAGE_INDEX) {
            yield ChoiceField::new('type', 'Type')
                ->setFormTypeOption('choice_label', fn(MenuItemType $value) => $value->label())
                ->formatValue(fn(MenuItemType $value) => $value->label());
        }

        yield BooleanField::new('enabled', 'Activé ?')->setFormTypeOption('label_attr.class', 'checkbox-switch');
        yield BooleanField::new('newWindow', 'Ouvrir dans un nouveau onglet ?')
            ->setFormTypeOption('label_attr.class', 'checkbox-switch')
            ->hideOnIndex();

        if ($menuItemType === MenuItemType::PAGE) {
            yield AssociationField::new('page', 'Page')
                ->autocomplete()
                ->setQueryBuilder(function (QueryBuilder $queryBuilder) {
                    $queryBuilder->andWhere('entity.enabled = 1');
                })
                ->hideOnIndex();
        }

        yield IntegerField::new('position', 'Position');
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $crudMenuId = Uuid::tryFromString($this->getRequest()->query->get('crudMenuId', ''));

        if (null !== $crudMenuId) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('entity.menu', ':crudMenuUuid'))
                ->setParameter('crudMenuUuid', $crudMenuId->toBinary());
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
                ->unset('crudMenuItemType')
                ->generateUrl(),
            Action::SAVE_AND_ADD_ANOTHER => $this->container->get(AdminUrlGenerator::class)->setAction(Action::NEW)->generateUrl(),
            default => $this->generateUrl($context->getDashboardRouteName()),
        };

        return $this->redirect($url);
    }

    private function returnToCrudMenuIndexAction(): RedirectResponse
    {
        return $this->redirect($this->adminUrlGenerator
            ->setController(MenuCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl());
    }
}
