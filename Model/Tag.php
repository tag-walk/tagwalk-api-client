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

use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Notable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\SlugTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class Tag extends AbstractDocument
{
    use NameTranslatable;
    use SlugTranslatable;
    use Notable;
    use Positionable;

    /**
     * @var Tag[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $similars;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $beauty = false;

    /**
     * @return Tag[]|null
     */
    public function getSimilars(): ?array
    {
        return $this->similars;
    }

    /**
     * @param Tag[]|null $similars
     */
    public function setSimilars(?array $similars)
    {
        $this->similars = $similars;
    }

    /**
     * @return bool|null
     */
    public function isBeauty(): ?bool
    {
        return $this->beauty;
    }

    /**
     * @param bool $beauty
     */
    public function setBeauty(bool $beauty)
    {
        $this->beauty = $beauty;
    }
}
