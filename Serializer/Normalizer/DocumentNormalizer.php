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

use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Tagwalk\ApiClientBundle\Model\Document;

/**
 * Normalizer for all Document instances.
 *
 * @extends ObjectNormalizer for nested properties but extract attributes only from object properties like PropertyNormalizer
 */
class DocumentNormalizer extends ObjectNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Document;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $context[self::IGNORED_ATTRIBUTES][] = 'enabled';
        $data = parent::normalize($object, $format, $context);
        if (false === empty($context['write'])) {
            unset($data['created_at'], $data['updated_at']);
        }

        return $data;
    }
}
