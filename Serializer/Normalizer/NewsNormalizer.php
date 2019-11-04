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
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\News;

/**
 * Normalizer for News instances.
 *
 * @extends DocumentNormalizer
 */
class NewsNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === News::class;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        foreach ($data['files'] as &$file) {
            $file = $this->serializer->denormalize($file, File::class);
        }
        if (false === empty($data['cover'])) {
            $data['cover'] = $this->serializer->denormalize($data['cover'], File::class);
        }
        if (false === empty($data['date'])) {
            $data['date'] = DateTime::createFromFormat(DATE_ATOM, $data['date']);
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
