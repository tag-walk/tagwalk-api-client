<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Add begin_at and end_at properties to a Document.
 */
trait Programmable
{
    /**
     * @var \DateTimeInterface|null
     * @Assert\DateTime()
     */
    protected $beginAt;

    /**
     * @var \DateTimeInterface|null
     * @Assert\DateTime()
     */
    protected $endAt;

    /**
     * @return \DateTimeInterface
     */
    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    /**
     * @param \DateTimeInterface $beginAt
     *
     * @return self
     */
    public function setBeginAt(?\DateTimeInterface $beginAt)
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    /**
     * @param \DateTimeInterface $endAt
     *
     * @return self
     */
    public function setEndAt(?\DateTimeInterface $endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }
}
