<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model\Traits
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait Descriptable
 *
 * Add description property and translations for available languages
 */
trait Descriptable
{
    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(min=2)
     */
    protected $description;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(min=2)
     */
    protected $descriptionEs;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(min=2)
     */
    protected $descriptionFr;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(min=2)
     */
    protected $descriptionIt;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(min=2)
     */
    protected $descriptionZh;

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return self
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionEs(): ?string
    {
        return $this->descriptionEs;
    }

    /**
     * @param string $descriptionEs
     *
     * @return self
     */
    public function setDescriptionEs(?string $descriptionEs): self
    {
        $this->descriptionEs = $descriptionEs;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionFr(): ?string
    {
        return $this->descriptionFr;
    }

    /**
     * @param string $descriptionFr
     *
     * @return self
     */
    public function setDescriptionFr(?string $descriptionFr): self
    {
        $this->descriptionFr = $descriptionFr;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionIt(): ?string
    {
        return $this->descriptionIt;
    }

    /**
     * @param string $descriptionIt
     *
     * @return self
     */
    public function setDescriptionIt(?string $descriptionIt): self
    {
        $this->descriptionIt = $descriptionIt;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescriptionZh(): ?string
    {
        return $this->descriptionZh;
    }

    /**
     * @param string $descriptionZh
     *
     * @return self
     */
    public function setDescriptionZh(?string $descriptionZh): self
    {
        $this->descriptionZh = $descriptionZh;

        return $this;
    }
}
