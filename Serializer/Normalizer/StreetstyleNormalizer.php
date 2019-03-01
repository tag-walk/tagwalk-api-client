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
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tagwalk\ApiClientBundle\Model\Affiliation;
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Model\Tag;

class StreetstyleNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var AffiliationNormalizer
     */
    private $affiliationNormalizer;

    /**
     * @var FileNormalizer
     */
    private $fileNormalizer;

    /**
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param AffiliationNormalizer $affiliationNormalizer
     * @param FileNormalizer $fileNormalizer
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        AffiliationNormalizer $affiliationNormalizer,
        FileNormalizer $fileNormalizer
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
        $this->affiliationNormalizer = $affiliationNormalizer;
        $this->fileNormalizer = $fileNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Streetstyle;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Streetstyle::class;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['city'])) {
            $data['city'] = $this->denormalize($data['city'], City::class);
        }
        if (isset($data['season'])) {
            $data['season'] = $this->denormalize($data['season'], Season::class);
        }
        if (!empty($data['designers'])) {
            foreach ($data['designers'] as &$designer) {
                $designer = $this->denormalize($designer, Designer::class);
            }
        }
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as &$tag) {
                $tag = $this->denormalize($tag, Tag::class);
            }
        }
        if (!empty($data['affiliations'])) {
            foreach ($data['affiliations'] as &$affiliation) {
                $affiliation = $this->affiliationNormalizer->denormalize($affiliation, Affiliation::class);
            }
        }
        if (!empty($data['files'])) {
            foreach ($data['files'] as &$file) {
                $file = $this->fileNormalizer->denormalize($file, File::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
