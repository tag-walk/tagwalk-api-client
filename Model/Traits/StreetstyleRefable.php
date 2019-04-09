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

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Tagwalk\ApiClientBundle\Model\Resource;
use Symfony\Component\Validator\Constraints as Assert;

trait StreetstyleRefable
{
    /**
     * @var Resource[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $streetstyles;

    /**
     * @return Resource[]|null
     */
    public function getStreetstyles(): ?array
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }

        return $this->streetstyles;
    }

    /**
     * @param Resource[]|null $streetstyles
     *
     * @return self
     */
    public function setStreetstyles(?array $streetstyles): self
    {
        if (null === $streetstyles) {
            $streetstyles = [];
        }
        $this->streetstyles = $streetstyles;

        return $this;
    }

    /**
     * Add an element to the streetstyle ressources collection
     *
     * @param Resource $streetstyle
     *
     * @return self
     */
    public function addStreetstyle(Resource $streetstyle): self
    {
        if (null === $this->streetstyles) {
            $this->streetstyles = [];
        }
        $this->streetstyles[] = $streetstyle;

        return $this;
    }

    /**
     * Remove an element from the streetstyle ressources collection
     *
     * @param string $uri
     *
     * @return bool
     */
    public function removeStreetstyle(string $uri): bool
    {
        foreach ($this->streetstyles as $key => $streetstyle) {
            if ($uri === $streetstyle->getUri()) {
                unset($this->streetstyles[$key]);

                return true;
            }
        }

        return false;
    }
}