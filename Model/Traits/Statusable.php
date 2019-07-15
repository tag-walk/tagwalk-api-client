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

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

/**
 * Trait Statusable.
 *
 * Add status property to a Document
 */
trait Statusable
{
    /**
     * @var string
     * @Assert\Choice(callback={"Tagwalk\ApiClientBundle\Utils\Constants\Status", "getAllowedValues"})
     */
    protected $status = Status::ENABLED;

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set status value to enable.
     *
     * @return void
     */
    public function enable()
    {
        $this->status = Status::ENABLED;
    }

    /**
     * Set status value to disable.
     *
     * @return void
     */
    public function disable()
    {
        $this->status = Status::DISABLED;
    }

    /**
     * Checks whether the object is enabled.
     *
     * @return bool true if is enabled, false otherwise
     */
    public function isEnabled()
    {
        return $this->status === Status::ENABLED;
    }
}
