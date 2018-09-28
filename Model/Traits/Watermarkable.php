<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model\Traits
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait Watermarkable
 *
 * Add watermark property to a Document
 *
 * @package Tagwalk\ApiClientBundle\Model\Traits
 */
trait Watermarkable
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $watermark;

    /**
     * @return string|null
     */
    public function getWatermark(): ?string
    {
        return $this->watermark;
    }

    /**
     * @param string|null $watermark
     *
     * @return self
     */
    public function setWatermark(?string $watermark): self
    {
        $this->watermark = $watermark;

        return $this;
    }
}
