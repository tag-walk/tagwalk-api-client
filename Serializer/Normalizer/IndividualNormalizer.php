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
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Individual;

class IndividualNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * @var FileNormalizer
     */
    private $fileNormalizer;

    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        FileNormalizer $fileNormalizer
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
        $this->fileNormalizer = $fileNormalizer;
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
            $data['cover'] = $this->fileNormalizer->denormalize($data['cover'], File::class);
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
