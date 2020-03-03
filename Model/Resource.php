<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Vincent Duruflé <vincent@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Sluggable;
use Tagwalk\ApiClientBundle\Model\Traits\Typeable;

/**
 * Uniform Resource Identifier
 */
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
     * @var \DateTimeInterface|null
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
     * @return \DateTimeInterface|null
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     *
     * @return self
     */
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
