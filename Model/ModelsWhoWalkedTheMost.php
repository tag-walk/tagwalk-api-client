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

class ModelsWhoWalkedTheMost
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
    private $season;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $city;

    /**
     * @var int|null
     * @Assert\Type("int")
     */
    private $length = 10;

    /**
     * @return null|string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param null|string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return null|string
     */
    public function getSeason(): ?string
    {
        return $this->season;
    }

    /**
     * @param null|string $season
     */
    public function setSeason(?string $season): void
    {
        $this->season = $season;
    }

    /**
     * @return null|string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param null|string $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return int|null
     */
    public function getLength(): ?int
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     */
    public function setLength(?int $length): void
    {
        $this->length = $length;
    }
}
