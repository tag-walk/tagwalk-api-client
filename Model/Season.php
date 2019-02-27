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

class Season extends AbstractDocument
{
    /**
     * @var string
     * @Assert\Type("string")
     */
    private $shortname;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $shopable = true;

    /**
     * @return null|string
     */
    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    /**
     * @param string $shortname
     */
    public function setShortname(string $shortname)
    {
        $this->shortname = $shortname;
    }

    /**
     * @return null|bool
     */
    public function getShopable(): ?bool
    {
        return $this->shopable;
    }

    /**
     * @param bool $shopable
     */
    public function setShopable(bool $shopable)
    {
        $this->shopable = $shopable;
    }
}
