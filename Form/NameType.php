<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => true,
            'attr' => [
                'class' => 'nameInput',
                'autocomplete' => 'off'
            ],
            'help' => 'help.name',
            'translation_domain' => 'forms'
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
