<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ExportDesigners extends Export
{
    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    private $splitSeason = false;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $type = 'street';

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $season;

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
     * @return bool
     */
    public function isSplitSeason(): bool
    {
        return $this->splitSeason;
    }

    /**
     * @param bool $splitSeason
     */
    public function setSplitSeason(bool $splitSeason): void
    {
        $this->splitSeason = $splitSeason;
    }

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
    public function getSeason(): ?string
    {
        return $this->season;
    }

    /**
     * @param string|null $season
     */
    public function setSeason(?string $season): void
    {
        $this->season = $season;
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
}