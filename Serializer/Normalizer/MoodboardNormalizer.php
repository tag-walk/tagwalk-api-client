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
use Tagwalk\ApiClientBundle\Model\Moodboard;
use Tagwalk\ApiClientBundle\Model\User;

class MoodboardNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @var UserNormalizer
     */
    private $userNormalizer;

    /**
     * @param NameConverterInterface|null $nameConverter
     * @param PropertyAccessorInterface|null $propertyAccessor
     * @param UserNormalizer $userNormalizer
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null,
        UserNormalizer $userNormalizer
    ) {
        parent::__construct($nameConverter, $propertyAccessor);
        $this->userNormalizer = $userNormalizer;
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

        return parent::denormalize($data, $class, $format, $context);
    }
}
