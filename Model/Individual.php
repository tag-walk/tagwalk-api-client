<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Descriptable;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;

class Individual extends AbstractDocument
{
    use Coverable;
    use Descriptable;
    use Linkable;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $model;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $gender = 'female';

    /**
     * @var \DateTime|null
     * @Assert\Date()
     */
    private $birthdate;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $nationality;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $location;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $hairColor;

    /**
     * @var string|null
     * @Assert\Url()
     */
    private $instagram;

    /**
     * @var Agency[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $agencies;

    /**
     * @return bool
     */
    public function isModel(): bool
    {
        return $this->model;
    }

    /**
     * @param bool $model
     *
     * @return self
     */
    public function setModel(bool $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * @param string|null $gender
     *
     * @return self
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthdate(): ?\DateTime
    {
        return $this->birthdate;
    }

    /**
     * @param \DateTime|null $birthdate
     *
     * @return self
     */
    public function setBirthdate(?\DateTime $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    /**
     * @param string|null $nationality
     *
     * @return self
     */
    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param null|string $location
     *
     * @return self
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getHairColor(): ?string
    {
        return $this->hairColor;
    }

    /**
     * @param null|string $hairColor
     *
     * @return self
     */
    public function setHairColor(?string $hairColor): self
    {
        $this->hairColor = $hairColor;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    /**
     * @param null|string $instagram
     *
     * @return self
     */
    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;

        return $this;
    }

    /**
     * @return Agency[]|null
     */
    public function getAgencies(): ?array
    {
        return $this->agencies;
    }

    /**
     * @param Agency[]|null $agencies
     *
     * @return self
     */
    public function setAgencies(?array $agencies): self
    {
        $this->agencies = $agencies;

        return $this;
    }
}
