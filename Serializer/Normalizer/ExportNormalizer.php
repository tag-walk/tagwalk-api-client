<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Tagwalk\ApiClientBundle\Model\Export;

/**
 * Normalizer for File instances.
 *
 * @extends DocumentNormalizer
 */
class ExportNormalizer extends ObjectNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Export;
    }

    /**
     * @param Export $object
     * @param string $format
     * @param array  $context
     *
     * @return array|bool|float|int|string
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);
        if (false === empty($context['write'])) {
            unset($data['created_at']);
            unset($data['updated_at']);
        }

        return $data;
    }
}
