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
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Model\Streetstyle;

class GalleryNormalizer extends DocumentNormalizer implements NormalizerInterface
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
        return $data instanceof Gallery;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Gallery::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (false === empty($data['cover'])) {
            $data['cover'] = $this->serializer->denormalize($data['cover'], File::class);
        }
        if (false === empty($data['streetstyles'])) {
            foreach ($data['streetstyles'] as &$streetstyle) {
                $streetstyle = $this->serializer->denormalize($streetstyle, Streetstyle::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
