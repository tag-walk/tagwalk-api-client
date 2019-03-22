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
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Utils\Constants\MediaType;

class Live extends AbstractDocument
{
    use Positionable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 255
     * )
     */
    protected $title;

    /**
     * @var City|null
     */
    private $city;

    /**
     * @var Season
     * @Assert\NotBlank()
     */
    private $season;

    /**
     * @var CoverableDesigner[]
     * @Assert\Type("array")
     */
    private $designers;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $type = MediaType::WOMENSWEAR;

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return self
     */
    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Season
     */
    public function getSeason(): Season
    {
        return $this->season;
    }

    /**
     * @param Season $season
     *
     * @return self
     */
    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    /**
     * @return CoverableDesigner[]
     */
    public function getDesigners(): array
    {
        return $this->designers;
    }

    /**
     * @param CoverableDesigner[] $designers
     *
     * @return self
     */
    public function setDesigners(array $designers): self
    {
        $this->designers = $designers;

        return $this;
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
     *
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
