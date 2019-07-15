<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Utils\Constants\LinkTarget;

/**
 * Trait Linkable.
 *
 * Add link property to a Document
 */
trait Linkable
{
    /**
     * @var string|null
     * @Assert\Url()
     */
    protected $link;

    /**
     * @var string
     * @Assert\Choice(LinkTarget::VALUES)
     */
    protected $linkTarget = '_self';

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     *
     * @return self
     */
    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkTarget(): string
    {
        return $this->linkTarget;
    }

    /**
     * @param string $linkTarget
     *
     * @return self
     */
    public function setLinkTarget(string $linkTarget): self
    {
        $this->linkTarget = $linkTarget;

        return $this;
    }
}
