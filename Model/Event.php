<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Vincent Duruflé <vincent@tag-walk.com>
 * @copyright   2021 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;

class Event extends AbstractDocument
{
    use Coverable;

    /**
     * @Assert\Type("string")
     */
    private ?string $date = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $type = null;

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(?string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
