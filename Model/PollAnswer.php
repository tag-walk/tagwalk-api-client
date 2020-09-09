<?php

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Identifiable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class PollAnswer
{
    use Identifiable, Textable;

    /**
     * @Assert\Type("integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    private int $votesCount = 0;

    public function getVotesCount(): int
    {
        return $this->votesCount;
    }

    public function setVotesCount(int $count): self
    {
        $this->votesCount = $count;

        return $this;
    }
}
