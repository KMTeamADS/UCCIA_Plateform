<?php

declare(strict_types=1);

namespace ADS\UCCIA\EasyAdmin\Field;

use A2lix\TranslationFormBundle\Form\Type\TranslationsType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class TranslationsSimpleField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null, $fieldsConfig = []): self
    {
        return new self()
            ->setProperty($propertyName)
            ->setLabel($label)
            ->onlyOnForms()
            ->setRequired(true)
            ->setFormType(TranslationsType::class)
            ->setFormTypeOptions([
                'fields' => $fieldsConfig,
            ]);
    }
}
