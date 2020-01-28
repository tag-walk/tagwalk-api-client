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
use Tagwalk\ApiClientBundle\Model\Export;

/**
 * ExportNormalizer use different properties string case than other Documents (spinal-case).
 *
 * @extends DocumentNormalizer
 */
class ExportNormalizer extends ObjectNormalizer
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Export;
    }
}
