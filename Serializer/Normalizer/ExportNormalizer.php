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

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tagwalk\ApiClientBundle\Model\ExportTags;

/**
 * Normalizer for File instances
 *
 * @extends DocumentNormalizer
 */
class ExportNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ExportTags;
    }

    /**
     * @param ExportTags $object
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
}
