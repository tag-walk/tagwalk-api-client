<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class Offer extends AbstractDocument
{
    use Positionable;
    use Textable;
    use Coverable;

    /**
     * @var int|null
     * @Assert\Type("int")
     * @Assert\GreaterThanOrEqual(value="1", message="Position must be positive")
     */
    protected $position;

    /**
     * @var int|null
     */
    private $price;

    /**
     * @var File|null
     */
    private $preview;

    /**
     * @return int|null
     */
    public function getPrice(): ?int
    {
        return $this->price;
    }

    /**
     * @param int|null $price
     *
     * @return self
     */
    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getPreview(): ?File
    {
        return $this->preview;
    }

    /**
     * @param File|null $preview
     *
     * @return self
     */
    public function setPreview(?File $preview): self
    {
        $this->preview = $preview;

        return $this;
    }
}
