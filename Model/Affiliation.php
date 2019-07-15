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
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

class Affiliation extends AbstractDocument
{
    use NameTranslatable;
    use Positionable;
    use Linkable;

    /**
     * @var Seller|null
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $seller;

    /**
     * @return null|Seller
     */
    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    /**
     * @param Seller|null $seller
     */
    public function setSeller(?Seller $seller)
    {
        $this->seller = $seller;
    }
}
