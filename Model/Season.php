<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

/**
 * Describe a Season Document
 *
 * @see Document
 */
class Season extends AbstractDocument
{
    use Positionable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 200
     * )
     */
    protected $name;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Regex("/^[a-z0-9]+(?:-[a-z0-9]+)*$/")
     */
    protected $slug;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $shortname;
    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $shopable = true;

    /**
     * @return string
     */
    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    /**
     * @param string $shortname
     * @return Season
     */
    public function setShortname(string $shortname): Season
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShopable(): ?bool
    {
        return $this->shopable;
    }

    /**
     * @param bool $shopable
     *
     * @return self
     */
    public function setShopable(?bool $shopable): self
    {
        $this->shopable = $shopable;

        return $this;
    }
}
