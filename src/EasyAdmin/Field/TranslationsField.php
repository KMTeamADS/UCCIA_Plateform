<?php

declare(strict_types = 1);

namespace ADS\UCCIA\EasyAdmin\Field;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class TranslationsField  implements FieldInterface
{
    use FieldTrait;

    public const string OPTION_FIELDS_CONFIG = 'fieldsConfig';

    public static function new(string $propertyName, ?string $label = null): self
    {
        return new self()
            ->setProperty($propertyName)
            ->setLabel($label)
            ->onlyOnForms()
            ->setRequired(true)
            ->addFormTheme('admin/crud/form/field/translations.html.twig')
            // ->addCssFiles('build/translations-field.css')
            ->setFormType(TranslationsType::class)
            ->setFormTypeOption('block_prefix', 'translations_field');
    }

    public function addTranslatableField(FieldInterface $field): self
    {
        $fieldsConfig = (array) $this->getAsDto()->getCustomOption(self::OPTION_FIELDS_CONFIG);
        $fieldsConfig[] = $field;

        $this->setCustomOption(self::OPTION_FIELDS_CONFIG, $fieldsConfig);

        return $this;
    }
}
