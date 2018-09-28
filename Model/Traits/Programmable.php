<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model\Traits
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model\Traits;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Add begin_at and end_at properties to a Document
 */
trait Programmable
{
    /**
     * @var \DateTime|null
     * @Assert\DateTime()
     */
    protected $beginAt;

    /**
     * @var \DateTime|null
     * @Assert\DateTime()
     */
    protected $endAt;

    /**
     * @return \DateTime
     */
    public function getBeginAt(): ?\DateTime
    {
        return $this->beginAt;
    }

    /**
     * @param \DateTime $beginAt
     *
     * @return self
     */
    public function setBeginAt(?\DateTime $beginAt)
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    /**
     * @param \DateTime $endAt
     *
     * @return self
     */
    public function setEndAt(?\DateTime $endAt)
    {
        $this->endAt = $endAt;

        return $this;
    }
}
