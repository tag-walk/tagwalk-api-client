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

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Model\Live;
use Tagwalk\ApiClientBundle\Model\Season;

/**
 * Normalizer for Live instances
 *
 * @extends DocumentNormalizer
 */
class LiveNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Live;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Live::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (false === empty($data['city'])) {
            $data['city'] = $this->serializer->denormalize($data['city'], City::class, $format, $context);
        }
        if (false === empty($data['season'])) {
            $data['season'] = $this->serializer->denormalize($data['season'], Season::class, $format, $context);
        }
        if (false === empty($data['designers'])) {
            foreach ($data['designers'] as &$designer) {
                $designer = $this->serializer->denormalize($designer, Designer::class, $format, $context);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
