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
use Tagwalk\ApiClientBundle\Model\Traits\Citieable;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;

class Event extends AbstractDocument
{
    use Coverable;
    use Citieable;

    public array $customFields = [];

    /**
     * @Assert\Type("string")
     */
    private ?string $description = null;

    private ?\DateTimeInterface $date = null;

    /**
     * @Assert\Type("string")
     */
    private ?string $type = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
