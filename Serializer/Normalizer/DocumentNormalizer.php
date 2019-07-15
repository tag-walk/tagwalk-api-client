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

use DateTime;
use ReflectionObject;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
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
    public function __construct(
        ClassMetadataFactoryInterface $classMetadataFactory = null,
        NameConverterInterface $nameConverter = null,
        PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct($classMetadataFactory, $nameConverter, $propertyAccessor);
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
        $reflectionObject = new ReflectionObject($object);
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

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data['created_at'])) {
            $data['created_at'] = DateTime::createFromFormat(DATE_ATOM, $data['created_at']);
        }
        if (isset($data['updated_at'])) {
            $data['updated_at'] = DateTime::createFromFormat(DATE_ATOM, $data['updated_at']);
        }

        return parent::denormalize($data, $class, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $data = parent::normalize($object, $format, $context);
        if (false === empty($context['write'])) {
            unset($data['created_at'], $data['updated_at']);
        }

        return $data;
    }
}
