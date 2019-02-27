<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class City extends AbstractDocument
{
    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $main = true;

    /**
     * @return bool|null
     */
    public function getMain(): ?bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     */
    public function setMain(bool $main)
    {
        $this->main = $main;
    }
}
