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

class ModelsIndex
{
    /**
     * @var string
     */
    private $season;

    /**
     * @var array
     */
    private $cities;

    /**
     * @var bool
     */
    private $global;

    /**
     * @return string
     */
    public function getSeason(): ?string
    {
        return $this->season;
    }

    /**
     * @param string $season
     */
    public function setSeason(?string $season): void
    {
        $this->season = $season;
    }

    /**
     * @return array
     */
    public function getCities(): ?array
    {
        return $this->cities;
    }

    /**
     * @param array $cities
     */
    public function setCities(?array $cities): void
    {
        $this->cities = $cities;
    }

    /**
     * @return bool
     */
    public function isGlobal(): ?bool
    {
        return $this->global;
    }

    /**
     * @param bool $global
     */
    public function setGlobal(?bool $global): void
    {
        $this->global = $global;
    }
}
