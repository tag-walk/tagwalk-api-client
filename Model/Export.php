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

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

abstract class Export
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $filename;

    /**
     * @var string|null
     * @Assert\Email
     */
    protected $email;

    /**
     * @var string|null
     * @Assert\Length(min="1", max="1")
     */
    protected $delimiter = ',';

    /**
     * @var bool|null
     * @Assert\Type("bool")
     */
    protected $keepEmpty = false;

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     */
    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    /**
     * @param string|null $delimiter
     */
    public function setDelimiter(?string $delimiter): void
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return bool
     */
    public function isKeepEmpty(): bool
    {
        return $this->keepEmpty;
    }

    /**
     * @param bool $keepEmpty
     */
    public function setKeepEmpty(bool $keepEmpty): void
    {
        $this->keepEmpty = $keepEmpty;
    }
}
