<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;

class DesignerSeason
{
    use Descriptable;

    /**
     * @Assert\Type("string")
     */
    private string $designer;

    /**
     * @Assert\Type("string")
     */
    private string $season;

    public function getDesigner(): string
    {
        return $this->designer;
    }

    public function setDesigner(string $designer): self
    {
        $this->designer = $designer;

        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): self
    {
        $this->season = $season;

        return $this;
    }
}
