<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Affiliation;
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Model\Tag;

class MediaNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Media;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Media::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['city'])) {
            $data['city'] = $this->serializer->denormalize($data['city'], City::class);
        }
        if (isset($data['season'])) {
            $data['season'] = $this->serializer->denormalize($data['season'], Season::class);
        }
        if (isset($data['designer'])) {
            $data['designer'] = $this->serializer->denormalize($data['designer'], Designer::class);
        }
        if (!empty($data['tags'])) {
            foreach ($data['tags'] as &$tag) {
                $tag = $this->serializer->denormalize($tag, Tag::class);
            }
        }
        if (!empty($data['files'])) {
            foreach ($data['files'] as &$file) {
                $file = $this->serializer->denormalize($file, File::class);
            }
        }
        if (!empty($data['affiliations'])) {
            foreach ($data['affiliations'] as &$affiliation) {
                $affiliation = $this->serializer->denormalize($affiliation, Affiliation::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
