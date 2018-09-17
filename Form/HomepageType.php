<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class HomepageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('section', HomepageSectionType::class)
            ->add('name', NameType::class)
            ->add('slug', SlugType::class)
            ->add('beginAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text'
            ])
            ->add('status', HiddenType::class, ['data' => Status::DISABLED])
            ->add('cells', HiddenType::class)
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Homepage::class,
            'translation_domain' => 'form'
        ]);
    }
}