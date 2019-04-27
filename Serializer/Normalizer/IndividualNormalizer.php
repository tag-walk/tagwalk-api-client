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

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Agency;
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Individual;

class IndividualNormalizer extends DocumentNormalizer implements NormalizerInterface
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
        return $data instanceof Individual;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Individual::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (false === empty($data['cover'])) {
            $data['cover'] = $this->serializer->denormalize($data['cover'], File::class);
        }
        if (false === empty($data['birthdate'])) {
            $data['birthdate'] = \DateTime::createFromFormat(DATE_ISO8601, $data['birthdate']);
        }
        if (false === empty($data['agencies'])) {
            foreach ($data['agencies'] as &$agency) {
                $agency = $this->serializer->denormalize($agency, Agency::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
