<?php

namespace Tagwalk\ApiClientBundle\Model;

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
}
