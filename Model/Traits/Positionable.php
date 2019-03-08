<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model\Traits
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait Positionable
 *
 * Add position property to a Document
 *
 * @package Tagwalk\ApiClientBundle\Model\Traits
 */
trait Positionable
{
    /**
     * @var int|null
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(value="0", message="Position must be positive")
     */
    protected $position;

    /**
     * @return int|null
     */
    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @param int|null $position
     *
     * @return self
     */
    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
