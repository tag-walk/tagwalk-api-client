<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tagwalk\ApiClientBundle\Model\File;

/**
 * Normalizer for File instances
 *
 * @extends DocumentNormalizer
 */
class FileNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof File;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === File::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['created_at'])) {
            $data['created_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['created_at']);
        }
        if (isset($data['updated_at'])) {
            $data['updated_at'] = \DateTime::createFromFormat(DATE_ISO8601, $data['updated_at']);
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
