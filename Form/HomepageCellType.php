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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Model\HomepageCell;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class HomepageCellType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', TextType::class, ['label_format' => 'label.homepage.cell.%name%'])
            ->add('link', TextType::class, ['label_format' => 'label.homepage.cell.%name%'])
            ->add('width', NumberType::class, ['label_format' => 'label.homepage.cell.%name%'])
            //TODO FILTERS
//            ->add('filters', CollectionType::class, [
//                'label_format' => 'label.homepage.cell.%name%',
//                'entry_type' => TextType::class,
//                'allow_add' => true,
//                'allow_delete' => true,
//                'delete_empty' => true
//            ])
            ->add('name', NameType::class, ['label_format' => 'label.%name%'])
            ->add('type', ChoiceType::class, ['label_format' => 'label.homepage.cell.%name%'])
            ->add('Position', HiddenType::class)
            ->add('slug', SlugType::class, ['label_format' => 'label.%name%'])
            ->add('status', HiddenType::class, ['data' => Status::ENABLED]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => HomepageCell::class,
            'translation_domain' => 'forms',
        ]);
    }
}
