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
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Model\Moodboard;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Model\User;

class MoodboardNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var UserNormalizer
     */
    private $userNormalizer;

    /**
     * @var MediaNormalizer
     */
    private $mediaNormalizer;

    /**
     * @var StreetstyleNormalizer
     */
    private $streetstyleNormalizer;

    /**
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param UserNormalizer $userNormalizer
     * @param MediaNormalizer $mediaNormalizer
     * @param StreetstyleNormalizer $streetstyleNormalizer
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        UserNormalizer $userNormalizer,
        MediaNormalizer $mediaNormalizer,
        StreetstyleNormalizer $streetstyleNormalizer
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
        $this->userNormalizer = $userNormalizer;
        $this->mediaNormalizer = $mediaNormalizer;
        $this->streetstyleNormalizer = $streetstyleNormalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Moodboard;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Moodboard::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['user'])) {
            $data['user'] = $this->userNormalizer->denormalize($data['user'], User::class);
        }
        if (!empty($data['medias'])) {
            foreach ($data['medias'] as &$media) {
                $media = $this->mediaNormalizer->denormalize($media, Media::class);
            }
        }
        if (!empty($data['streetstyles'])) {
            foreach ($data['streetstyles'] as &$streetstyle) {
                $streetstyle = $this->streetstyleNormalizer->denormalize($streetstyle, Streetstyle::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
