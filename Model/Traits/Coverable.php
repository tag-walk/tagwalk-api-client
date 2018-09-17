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
use Tagwalk\ApiClientBundle\Model\File;

/**
 * Trait Coverable
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
    private $cover;

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
