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
 * Trait NameTranslatable
 *
 * Add translations for name property to a Document
 */
trait NameTranslatable
{
    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 200
     * )
     */
    protected $nameEs;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 200
     * )
     */
    protected $nameFr;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 2,
     *      max = 200
     * )
     */
    protected $nameIt;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $nameZh;

    /**
     * @return string
     */
    public function getNameEs(): ?string
    {
        return $this->nameEs;
    }

    /**
     * @param string $nameEs
     *
     * @return self
     */
    public function setNameEs(?string $nameEs): self
    {
        $this->nameEs = $nameEs;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameFr(): ?string
    {
        return $this->nameFr;
    }

    /**
     * @param string $nameFr
     *
     * @return self
     */
    public function setNameFr(?string $nameFr): self
    {
        $this->nameFr = $nameFr;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameIt(): ?string
    {
        return $this->nameIt;
    }

    /**
     * @param string $nameIt
     *
     * @return self
     */
    public function setNameIt(?string $nameIt): self
    {
        $this->nameIt = $nameIt;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameZh(): ?string
    {
        return $this->nameZh;
    }

    /**
     * @param string $nameZh
     *
     * @return self
     */
    public function setNameZh(?string $nameZh): self
    {
        $this->nameZh = $nameZh;

        return $this;
    }
}
