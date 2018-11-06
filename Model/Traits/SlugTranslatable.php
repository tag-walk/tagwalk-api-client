<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

/**
 * Trait SlugTranslatable
 *
 * Add slug translation properties to a Document
 */
trait SlugTranslatable
{
    /**
     * @var string|null
     */
    protected $slugEs;

    /**
     * @var string|null
     */
    protected $slugFr;

    /**
     * @var string|null
     */
    protected $slugIt;

    /**
     * @var string|null
     */
    protected $slugZh;

    /**
     * @return null|string
     */
    public function getSlugEs(): ?string
    {
        return $this->slugEs;
    }

    /**
     * @param null|string $slugEs
     *
     * @return self
     */
    public function setSlugEs(?string $slugEs): self
    {
        $this->slugEs = $slugEs;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSlugFr(): ?string
    {
        return $this->slugFr;
    }

    /**
     * @param null|string $slugFr
     *
     * @return self
     */
    public function setSlugFr(?string $slugFr): self
    {
        $this->slugFr = $slugFr;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSlugIt(): ?string
    {
        return $this->slugIt;
    }

    /**
     * @param null|string $slugIt
     *
     * @return self
     */
    public function setSlugIt(?string $slugIt): self
    {
        $this->slugIt = $slugIt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSlugZh(): ?string
    {
        return $this->slugZh;
    }

    /**
     * @param null|string $slugZh
     *
     * @return self
     */
    public function setSlugZh(?string $slugZh): self
    {
        $this->slugZh = $slugZh;

        return $this;
    }
}
