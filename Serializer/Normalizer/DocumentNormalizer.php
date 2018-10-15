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

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Tagwalk\ApiClientBundle\Model\Document;

/**
 * Normalizer for all Document instances
 *
 * @extends ObjectNormalizer for nested properties but extract attributes only from object properties like PropertyNormalizer
 */
class DocumentNormalizer extends ObjectNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function __construct(
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct(null, $nameConverter, $propertyAccessor);
    }

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
    protected function extractAttributes($object, $format = null, array $context = [])
    {
        $reflectionObject = new \ReflectionObject($object);
        $attributes = [];
        do {
            foreach ($reflectionObject->getProperties() as $property) {
                if (!$this->isAllowedAttribute($reflectionObject->getName(), $property->name)) {
                    continue;
                }

                $attributes[] = $property->name;
            }
        } while ($reflectionObject = $reflectionObject->getParentClass());

        return $attributes;
    }
}
