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

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Utils\Reindexer;

trait Streetstyleable
{
    /**
     * @var Streetstyle[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    protected $streetstyles;

    /**
     * Get the streetstyles collection.
     *
     * @return Streetstyle[]|null
     */
    public function getStreetstyles(): ?array
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }

        return $this->streetstyles;
    }

    /**
     * Set the streetstyle collection.
     *
     * @param Streetstyle[]|null $streetstyles
     *
     * @return self
     */
    public function setStreetstyles(?array $streetstyles): self
    {
        if (null === $streetstyles) {
            $streetstyles = [];
        }
        $this->streetstyles = $streetstyles;
        Reindexer::reindex($this->streetstyles);

        return $this;
    }

    /**
     * Add an element to the streetstyle collection.
     *
     * @param Streetstyle $streetstyle
     *
     * @return self
     */
    public function addStreetstyle(Streetstyle $streetstyle): self
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }
        if (null === $streetstyle->getPosition()) {
            $streetstyle->setPosition(count($this->streetstyles) + 1);
        }
        $this->streetstyles[] = $streetstyle;
        Reindexer::reindex($this->streetstyles);

        return $this;
    }

    /**
     * Remove an element from the streetstyle collection.
     *
     * @param string $slug
     *
     * @return bool
     */
    public function removeStreetstyle(string $slug): bool
    {
        foreach ($this->streetstyles as $key => $streetstyle) {
            if ($slug === $streetstyle->getSlug()) {
                unset($this->streetstyles[$key]);

                return true;
            }
        }
        Reindexer::reindex($this->streetstyles);

        return false;
    }
}
