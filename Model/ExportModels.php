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

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ExportModels extends Export
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $type = 'woman';

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $seasons;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $designers;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $city;

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $splitDesigner = false;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSeasons(): ?string
    {
        return $this->seasons;
    }

    /**
     * @param string|null $seasons
     */
    public function setSeasons(?string $seasons): void
    {
        $this->seasons = $seasons;
    }

    /**
     * @return string|null
     */
    public function getDesigners(): ?string
    {
        return $this->designers;
    }

    /**
     * @param string|null $designers
     */
    public function setDesigners(?string $designers): void
    {
        $this->designers = $designers;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return bool|null
     */
    public function getSplitDesigner(): ?bool
    {
        return $this->splitDesigner;
    }

    /**
     * @param bool|null $splitDesigner
     */
    public function setSplitDesigner(?bool $splitDesigner): void
    {
        $this->splitDesigner = $splitDesigner;
    }
}
