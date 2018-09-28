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
use Tagwalk\ApiClientBundle\Model\File;
use Tagwalk\ApiClientBundle\Model\HomepageCell;

/**
 * Normalizer for HomepageCell instances
 *
 * @extends DocumentNormalizer
 */
class HomepageCellNormalizer extends DocumentNormalizer implements NormalizerInterface
{
    /**
     * @var FileNormalizer
     */
    private $fileNormalizer;

    /**
     * {@inheritdoc}
     */
    public function __construct(FileNormalizer $fileNormalizer)
    {
        parent::__construct();
        $this->fileNormalizer = $fileNormalizer;
    }
    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof HomepageCell;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === HomepageCell::class;
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
        if (false === empty($data['files'])) {
            foreach ($data['files'] as &$file) {
                $file = $this->fileNormalizer->denormalize($file, File::class);
            }
        }

        return parent::denormalize($data, $class, $format, $context);
    }
}
