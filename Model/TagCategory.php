<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Vincent Duruflé <vincent@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class TagCategory extends AbstractDocument
{
    /**
     * @var Acl
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private Acl $acl;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return Acl
     */
    public function getAcl(): Acl
    {
        return $this->acl;
    }

    /**
     * @param Acl $acl
     *
     * @return self
     */
    public function setAcl(Acl $acl): self
    {
        $this->acl = $acl;

        return $this;
    }
}
