<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Serializer\Normalizer
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Model\HomepageCell;

/**
 * Normalizer for Homepage instances
 *
 * @extends DocumentNormalizer
 */
class HomepageNormalizer extends DocumentNormalizer implements NormalizerInterface, DenormalizerInterface
{
    private $cellNormalizer;

    /**
     * {@inheritdoc}
     */
    public function __construct(HomepageCellNormalizer $cellNormalizer)
    {
        parent::__construct();
        $this->cellNormalizer = $cellNormalizer;
    }

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
     * @param Homepage $object
     * @param string $format
     * @param array $context
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach ($data['cells'] as &$cell) {
            $cell = $this->cellNormalizer->denormalize($cell, HomepageCell::class);
        }
        if (isset($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['created_at']);
        }
        if (isset($data['updated_at'])) {
            $data['updated_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['updated_at']);
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
