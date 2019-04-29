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

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Model\HomepageCell;

/**
 * Normalizer for Homepage instances
 *
 * @extends DocumentNormalizer
 */
class HomepageNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
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
        return $data instanceof Homepage;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Homepage::class;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach ($data['cells'] as &$cell) {
            $cell = $this->serializer->denormalize($cell, HomepageCell::class);
        }
        if (isset($data['begin_at'])) {
            $data['begin_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['begin_at']);
        }
        if (isset($data['end_at'])) {
            $data['end_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['end_at']);
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
