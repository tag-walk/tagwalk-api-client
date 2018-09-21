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

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tagwalk\ApiClientBundle\Model\Homepage;

/**
 * Normalizer for HomepageCell instances
 *
 * @extends DocumentNormalizer
 */
class HomepageNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    public function __construct()
    {
        parent::__construct(null, new CamelCaseToSnakeCaseNameConverter());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Homepage;
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
}
