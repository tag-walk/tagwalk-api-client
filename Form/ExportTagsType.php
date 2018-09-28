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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Tagwalk\ApiClientBundle\Model\Export;

class ExportTagsType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('export_tags'))
            ->add('email', TextType::class, ['required' => false])
            ->add('filename', TextType::class, ['required' => false])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Womenswear' => 'woman',
                    'Menswear' => 'man',
                    'Womenswear accessories' => 'accessory',
                    'Menswear accessories' => 'accessory-man',
                    'Couture' => 'couture',
                    'Streetstyles' => 'street',
                ],
            ])
            ->add('season', TextType::class, ['required' => false])
            ->add('designer', TextType::class, ['required' => false])
            ->add('city', TextType::class, ['required' => false])
            ->add('keepEmpty', CheckboxType::class, ['required' => false])
            ->add('splitCity', CheckboxType::class, ['required' => false])
            ->add('splitDesigner', CheckboxType::class, ['required' => false])
            ->add('splitSeason', CheckboxType::class, ['required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Generate', 'attr' => ['class' => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Export::class,
            'translation_domain' => 'export'
        ]);
    }
}