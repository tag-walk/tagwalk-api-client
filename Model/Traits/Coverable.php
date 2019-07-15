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
use Tagwalk\ApiClientBundle\Model\File;

/**
 * Trait Coverable.
 *
 * Add cover file property to a document
 */
trait Coverable
{
    /**
     * @var File|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    protected $cover;

    /**
     * @return File|null
     */
    public function getCover(): ?File
    {
        return $this->cover;
    }

    /**
     * @param File|null $cover
     *
     * @return self
     */
    public function setCover(?File $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
