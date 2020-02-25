<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Member extends AbstractDocument
{
    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    private $job;

    /**
     * @return string
     */
    public function getJob(): string
    {
        return $this->job;
    }

    /**
     * @param string $job
     *
     * @return self
     */
    public function setJob(string $job): self
    {
        $this->job = $job;

        return $this;
    }
}
