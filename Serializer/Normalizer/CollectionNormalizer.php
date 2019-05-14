<?php

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Model\Collection;
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Model\Season;

class CollectionNormalizer extends DocumentNormalizer
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
        return $data instanceof Collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Collection::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (false === empty($data['city'])) {
            $data['city'] = $this->serializer->denormalize($data['city'], City::class);
        }
        if (false === empty($data['season'])) {
            $data['season'] = $this->serializer->denormalize($data['season'], Season::class);
        }
        if (false === empty($data['designer'])) {
            $data['designer'] = $this->serializer->denormalize($data['designer'], Designer::class);
        }
        if (false === empty($data['cover'])) {
            $data['cover'] = $this->serializer->denormalize($data['cover'], File::class);
        }
        if (false === empty($data['medias'])) {
            foreach ($data['medias'] as &$media) {
                $media = $this->serializer->denormalize($media, Media::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
