<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Timestampable;

class PollAnswer
{
    use Timestampable;

    /**
     * @Assert\Type("integer")
     */
    private ?int $pollId;

    /**
     * @Assert\Type("integer")
     */
    private ?int $choiceId;

    /**
     * @var User|null
     * @Assert\Type("object")
     */
    protected $user;

    public function setPollId(?int $pollId): self
    {
        $this->pollId = $pollId;

        return $this;
    }

    public function getPollId(): ?int
    {
        return $this->pollId;
    }

    public function setChoiceId(?int $choiceId): self
    {
        $this->choiceId = $choiceId;

        return $this;
    }

    public function getChoiceId(): ?int
    {
        return $this->choiceId;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
