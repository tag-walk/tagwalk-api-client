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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class HomepageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('section', HomepageSectionType::class, ['label_format' => 'label.homepage.%name%',])
            ->add('name', NameType::class, ['label_format' => 'label.%name%'])
            ->add('slug', SlugType::class, ['label_format' => 'label.%name%'])
            ->add('beginAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label_format' => 'label.%name%',
                'help' => 'help.beginAt',
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label_format' => 'label.%name%',
                'help' => 'help.endAt',
            ])
            ->add('status', HiddenType::class, ['data' => Status::DISABLED])
            ->add('cells', CollectionType::class, [
                'entry_type' => HomepageCellType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Create']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Homepage::class,
            'translation_domain' => 'forms'
        ]);
    }
}