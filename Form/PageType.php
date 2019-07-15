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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Model\Page;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class PageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', NameType::class, ['label_format' => 'label.%name%'])
            ->add('slug', SlugType::class, [
                'label_format' => 'label.%name%',
                'attr' => ['readonly' => true],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => Status::getOptions(),
            ])
            ->add('text', TextareaType::class, [
                'required' => false,
            ])
            ->add('text_fr', TextareaType::class, ['required' => false])
            ->add('text_es', TextareaType::class, ['required' => false])
            ->add('text_it', TextareaType::class, ['required' => false])
            ->add('text_zh', TextareaType::class, ['required' => false])
            ->add('submit', SubmitType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
            'translation_domain' => 'forms',
        ]);
    }
}
