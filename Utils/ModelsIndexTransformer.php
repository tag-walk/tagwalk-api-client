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

namespace Tagwalk\ApiClientBundle\Utils;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Tagwalk\ApiClientBundle\Model\ModelsIndex;

class ModelsIndexTransformer
{
    /**
     * @var DenormalizerInterface
     */
    private $denormalizer;

    /**
     * @param DenormalizerInterface $denormalizer
     */
    public function __construct(DenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param array $configs
     * @param bool $denormalize
     * @return array|ModelsIndex
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function transform(array $configs, bool $denormalize = false)
    {
        $params = [];
        foreach ($configs as $config) {
            $split = explode('.', $config['key']);
            switch (end($split)) {
                case 'season':
                    $params['season'] = ($config['value']);
                    break;
                case 'cities':
                    $cities = explode(',', $config['value']);
                    $params['cities'] = $cities;
                    break;
                case 'global':
                    $params['global'] = boolval($config['value']);
                    break;
            }
        }
        if ($denormalize) {
            $params = $this->denormalizer->denormalize($params, ModelsIndex::class);
        }

        return $params;
    }
}
