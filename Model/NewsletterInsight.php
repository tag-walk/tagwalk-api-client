<?php

namespace Tagwalk\ApiClientBundle\Model;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;

class NewsletterInsight extends AbstractDocument
{
    use Coverable, Linkable, NameTranslatable;

    /** @Assert\DateTime() */
    protected ?DateTimeInterface $sentAt;

    public function getSentAt(): ?DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?DateTimeInterface $sentAt): NewsletterInsight
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}
