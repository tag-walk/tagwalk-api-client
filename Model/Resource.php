<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Sluggable;
use Tagwalk\ApiClientBundle\Model\Traits\Typeable;
use Symfony\Component\Validator\Constraints as Assert;

class Resource
{
    use Sluggable;
    use Typeable;
    use Positionable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(
     *      min = 6,
     *      max = 255
     * )
     */
    private $uri;
    /**
     * @var \DateTime|null
     * @Assert\DateTime()
     */
    private $date;

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     *
     * @return self
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     *
     * @return self
     */
    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }
}
