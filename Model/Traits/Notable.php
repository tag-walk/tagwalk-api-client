<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model\Traits
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait Notable
 *
 * Add note property to a Document
 *
 * @package Tagwalk\ApiClientBundle\Model\Traits
 */
trait Notable
{
    /**
     * @var int
     * @Assert\Type("int")
     * @Assert\Range(
     *      min = 1,
     *      max = 10
     * )
     */
    protected $note = 1;

    /**
     * @return int
     */
    public function getNote(): int
    {
        return $this->note;
    }

    /**
     * @param int $note
     *
     * @return self
     */
    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }
}
