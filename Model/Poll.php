<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class Poll extends AbstractDocument
{
    use Textable;

    /**
     * @Assert\Type("string")
     * @Assert\Regex("/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/")
     */
    private ?string $color;

    /**
     * @var PollChoice[]
     * @Assert\Type("array")
     */
    private array $choices = [];

    /**
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    private int $votesTotalCount = 0;

    public function setColor(?string $color): Poll
    {
        $this->color = $color;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    public function setChoices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public function addChoice(PollChoice $choice): self
    {
        if (!$this->containsChoice($choice)) {
            $this->choices[] = $choice;
        }

        return $this;
    }

    public function removeChoice(PollChoice $choice): self
    {
        foreach ($this->choices as $key => $existingChoice) {
            if ($existingChoice->getId() === $choice->getId()) {
                unset($this->choices[$key]);
            }
        }

        return $this;
    }

    public function containsChoice(PollChoice $choice): bool
    {
        foreach ($this->choices as $existingChoice) {
            if ($existingChoice->getId() === $choice->getId()) {
                return true;
            }
        }

        return false;
    }

    public function setVotesTotalCount(int $count): self
    {
        $this->votesTotalCount = max(0, $count);

        return $this;
    }

    public function getVotesTotalCount(): int
    {
        return $this->votesTotalCount;
    }
}
