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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tagwalk\ApiClientBundle\Model\ModelsWhoWalkedTheMost;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ModelsWhoWalkedTheMostType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @param RouterInterface $router
     * @param ApiProvider $apiProvider
     * @param RequestStack $requestStack
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RouterInterface $router, ApiProvider $apiProvider, RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->apiProvider = $apiProvider;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('models_who_walked_the_most'))
            ->add('type', ChoiceType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'autocomplete-type',
                    'data-placeholder' => 'Select a type'
                ],
                'choices' => [
                    'Womenswear' => 'woman',
                    'Menswear' => 'man',
                    'Womenswear accessories' => 'accessory',
                    'Menswear accessories' => 'accessory-man',
                    'Couture' => 'couture',
                    'Streetstyles' => 'street',
                ],
            ])
            ->add('season', ChoiceType::class, [
                'required' => false,
                'choices' => $this->getSeasons(),
                'validation_groups' => null,
                'attr' => [
                    'class' => 'autocomplete-season',
                    'data-placeholder' => 'Select a season filter'
                ]
            ])
            ->add('city', ChoiceType::class, [
                'required' => false,
                'choices' => $this->getCities(),
                'attr' => [
                    'class' => 'autocomplete-city',
                    'data-placeholder' => 'Select a city filter'
                ]
            ])
            ->add('length', NumberType::class, [
                'required' => false,
                'attr' => [
                    'data-placeholder' => 'Number of results to show'
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'View', 'attr' => ['class' => 'btn btn-primary']]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $form->remove('seasonsSelect');
            $form->remove('designersSelect');
        });
    }

    /**
     * @return array
     */
    private function getSeasons()
    {
        $query = [
            'size' => 100,
            'sort' => 'position:asc',
            'status' => 'enabled'
        ];
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $seasons = [];
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $column = $locale === 'en' ? 'name' : 'name_' . $locale;
        foreach ($data as $i => $datum) {
            $key = false === empty($datum[$column]) ? $datum[$column] : $datum['name'];
            $seasons[$key] = $datum['slug'];
        }

        return $seasons;
    }

    /**
     * @return array
     */
    private function getCities()
    {
        $locale = $this->requestStack->getCurrentRequest()->getLocale();
        $column = $locale === 'en' ? 'name' : 'name_' . $locale;
        $query = [
            'size' => 100,
            'sort' => $column . ':asc',
            'status' => 'enabled'
        ];
        $apiResponse = $this->apiProvider->request('GET', '/api/cities', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $cities = [];
        foreach ($data as $i => $datum) {
            $key = false === empty($datum[$column]) ? $datum[$column] : $datum['name'];
            $cities[$key] = $datum['slug'];
        }

        return $cities;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModelsWhoWalkedTheMost::class,
            'translation_domain' => 'models'
        ]);
    }
}
