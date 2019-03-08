<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Document
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Notable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\SlugTranslatable;

class Tag extends AbstractDocument
{
    use SlugTranslatable;
    use NameTranslatable;
    use Notable;
    use Positionable;

    /**
     * @var Tag[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $similars;

    /**
     * @var boolean
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
     *
     * @return self
     */
    public function setSimilars(?array $similars): self
    {
        $this->similars = $similars;

        return $this;
    }

    /**
     * @return bool
     */
    public function isBeauty(): bool
    {
        return $this->beauty;
    }


    /**
     * @param bool $beauty
     * @return self
     */
    public function setBeauty(bool $beauty): self
    {
        $this->beauty = $beauty;

        return $this;
    }
}
