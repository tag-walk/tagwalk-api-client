<?php

namespace Tagwalk\ApiClientBundle\Model;

use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Fileable;

class WornLook extends AbstractDocument
{
    use Fileable;

    /**
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private ?Individual $individual = null;

    /**
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private ?Event $event = null;

    /**
     * @Assert\NotBlank()
     */
    private string $lookSlug;

    /**
     * @Assert\Type("string")
     */
    private ?string $comment = null;

    /**
     * @Assert\DateTime()
     */
    private ?DateTimeInterface $wornAt = null;

    public function getIndividual(): ?Individual
    {
        return $this->individual;
    }

    public function setIndividual(Individual $individual): self
    {
        $this->individual = $individual;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getLookSlug(): string
    {
        return $this->lookSlug;
    }

    public function setLookSlug(string $lookSlug): self
    {
        $this->lookSlug = $lookSlug;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getWornAt(): ?DateTimeInterface
    {
        return $this->wornAt;
    }

    public function setWornAt(?DateTimeInterface $wornAt): self
    {
        $this->wornAt = $wornAt;

        return $this;
    }
}
