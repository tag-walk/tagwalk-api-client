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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tagwalk\ApiClientBundle\Model\ExportMoodboards;

class ExportMoodboardsType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('export_moodboards'))
            ->add('email', TextType::class, [
                'required' => false,
                'data' => $this->tokenStorage->getToken()->getUsername()
            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Womenswear' => 'woman',
                    'Menswear' => 'man',
                    'Womenswear accessories' => 'accessory',
                    'Menswear accessories' => 'accessory-man',
                    'Couture' => 'couture',
                    'Streetstyles' => 'street',
                ]
            ])
            ->add('designers', HiddenType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'export-designers'
                ]
            ])
            ->add('designersSelect', ChoiceType::class, [
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                'validation_groups' => null,
                'attr' => [
                    'data-path' => $this->router->generate('autocomplete_designer'),
                    'class' => 'autocomplete-designers',
                    'data-placeholder' => 'Filter on designers'
                ]
            ])
            ->add('filename', TextType::class, ['required' => false])
            ->add('submit', SubmitType::class, ['label' => 'Generate', 'attr' => ['class' => 'btn btn-primary']]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $form->remove('designersSelect');
        });
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExportMoodboards::class,
            'translation_domain' => 'export'
        ]);
    }
}
