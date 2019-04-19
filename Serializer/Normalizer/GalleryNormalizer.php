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
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Model\Streetstyle;

class GalleryNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * @var FileNormalizer
     */
    private $fileNormalizer;

    /**
     * @var StreetstyleNormalizer
     */
    private $streetstyleNormalizer;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        FileNormalizer $fileNormalizer,
        StreetstyleNormalizer $streetstyleNormalizer
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
        $this->fileNormalizer = $fileNormalizer;
        $this->streetstyleNormalizer = $streetstyleNormalizer;
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
            $data['cover'] = $this->fileNormalizer->denormalize($data['cover'], File::class);
        }
        if (false === empty($data['streetstyles'])) {
            foreach ($data['streetstyles'] as &$streetstyle) {
                $streetstyle = $this->streetstyleNormalizer->denormalize($streetstyle, Streetstyle::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
