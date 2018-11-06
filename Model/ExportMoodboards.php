<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class ExportMoodboards
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $filename;

    /**
     * @var string|null
     * @Assert\Email
     */
    private $email;

    /**
     * @var string|null
     * @Assert\Length(min="1", max="1")
     */
    private $delimiter = ',';

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $type = 'woman';

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $designers;

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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getDesigners(): ?string
    {
        return $this->designers;
    }

    /**
     * @param string|null $designers
     */
    public function setDesigners(?string $designers): void
    {
        $this->designers = $designers;
    }
}
