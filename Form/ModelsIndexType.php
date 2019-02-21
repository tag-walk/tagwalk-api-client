<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tagwalk\ApiClientBundle\Model\ModelsIndex;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ModelsIndexType extends AbstractType
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ApiProvider $apiProvider
     * @param RequestStack $requestStack
     */
    public function __construct(ApiProvider $apiProvider, RequestStack $requestStack)
    {
        $this->apiProvider = $apiProvider;
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('season', ChoiceType::class, [
                'label' => 'form.season',
                'choices' => $this->getSeasons()
            ])
            ->add('cities', ChoiceType::class, [
                'label' => 'form.cities',
                'choices' => $this->getCities(),
                'expanded' => true,
                'multiple' => true
            ])
            ->add('global', CheckboxType::class, [
                'label' => 'form.global',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.save',
                'attr' => [
                    'class' => 'btn-primary'
                ]
            ])
        ;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getSeasons()
    {
        $query = [
            'size' => 100,
            'sort' => 'position:asc',
            'status' => 'enabled',
            'language' => $this->requestStack->getCurrentRequest()->getLocale()
        ];
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $seasons = [];
        foreach ($data as $i => $datum) {
            $seasons[$datum['name']] = $datum['slug'];
        }

        return $seasons;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getCities()
    {
        $query = [
            'size' => 100,
            'sort' => 'name:asc',
            'status' => 'enabled',
            'language' => $this->requestStack->getCurrentRequest()->getLocale()
        ];
        $apiResponse = $this->apiProvider->request('GET', '/api/cities', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $cities = [];
        foreach ($data as $i => $datum) {
            if ($datum['slug'] !== 'florence') {
                $cities[$datum['name']] = $datum['slug'];
            }
        }

        return $cities;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModelsIndex::class,
            'translation_domain' => 'models'
        ]);
    }
}
