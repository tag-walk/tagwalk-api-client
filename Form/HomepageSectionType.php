<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Utils\Constants\HomepageSection;

class HomepageSectionType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'input.homepage.section.home' => HomepageSection::HOME,
                'input.homepage.section.shop' => HomepageSection::SHOP,
                'input.homepage.section.street' => HomepageSection::STREET,
            ],
            'help' => 'help.homepage.section',
            'translation_domain' => 'forms',
            'choice_translation_domain' => true,
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
