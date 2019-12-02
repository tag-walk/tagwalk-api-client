<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;

class Agency extends AbstractDocument
{
    use Descriptable;
    use Linkable;

    /**
     * @var boolean
     * @Assert\Type("boolean")
     */
    private $main = false;

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     *
     * @return self
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }
}
