<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Mediable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Utils\Constants\MediaType;

/**
 * Collection Document.
 *
 * @see Document
 */
class Collection extends AbstractDocument
{
    use NameTranslatable;
    use Coverable;
    use Mediable;

    /**
     * @var City
     * @Assert\Type("object")
     */
    private $city;

    /**
     * @var Season
     * @Assert\Type("object")
     */
    private $season;

    /**
     * @var Designer
     * @Assert\Type("object")
     */
    private $designer;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"Tagwalk\ApiClientBundle\Utils\Constants\MediaType", "getAllowedValues"})
     */
    private $type = MediaType::WOMENSWEAR;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $courtesy;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $embed;

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
     * @return Designer
     */
    public function getDesigner(): Designer
    {
        return $this->designer;
    }

    /**
     * @param Designer $designer
     *
     * @return self
     */
    public function setDesigner(Designer $designer): self
    {
        $this->designer = $designer;

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
     * @return string|null
     */
    public function getCourtesy(): ?string
    {
        return $this->courtesy;
    }

    /**
     * @param string|null $courtesy
     *
     * @return self
     */
    public function setCourtesy(?string $courtesy): self
    {
        $this->courtesy = $courtesy;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmbed(): ?string
    {
        return $this->embed;
    }

    /**
     * @param string|null $embed
     *
     * @return self
     */
    public function setEmbed(?string $embed): self
    {
        $this->embed = $embed;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s %s %s',
            $this->designer->getName(),
            $this->type,
            $this->season->getName(),
            $this->city->getName()
        );
    }
}
