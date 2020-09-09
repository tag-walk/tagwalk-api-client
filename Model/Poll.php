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
     * @var PollAnswer[]
     * @Assert\Type("array")
     */
    private array $answers = [];

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

    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function addAnswer(PollAnswer $answer): self
    {
        if (!$this->containsAnswer($answer)) {
            $this->answers[] = $answer;
        }

        return $this;
    }

    public function removeAnswer(PollAnswer $answer): self
    {
        foreach ($this->answers as $key => $existingAnswer) {
            if ($existingAnswer->getId() === $answer->getId()) {
                unset($this->answers[$key]);
            }
        }

        return $this;
    }

    public function containsAnswer(PollAnswer $answer): bool
    {
        foreach ($this->answers as $existingAnswer) {
            if ($existingAnswer->getId() === $answer->getId()) {
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
