<?php

namespace Tagwalk\ApiClientBundle\Utils;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\String\UnicodeString;

class FormErrorsMapper
{
    public static function mapApiErrorsToForm(FormInterface $form, array $errors): void
    {
        foreach ($errors as $propertyPath => $error) {
            $property = self::findProperty($form, $propertyPath);

            if ($property instanceof FormInterface) {
                $error = new FormError($error);
                $property->addError($error);

                continue;
            }

            $error = new FormError($propertyPath . ': ' . $error);
            $form->addError($error);
        }
    }

    private static function findProperty(FormInterface $form, string $propertyPath): ?FormInterface
    {
        if ($form->has($propertyPath) === true) {
            return $form->get($propertyPath);
        }

        $propertyPath = self::toSnakeCase($propertyPath);

        if ($form->has($propertyPath) === true) {
            return $form->get($propertyPath);
        }

        return null;
    }

    private static function toSnakeCase(string $toConvert): string
    {
        $toConvert = new UnicodeString($toConvert);

        return $toConvert->snake();
    }
}
