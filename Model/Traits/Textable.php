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
 * Add text property and translations for available languages
 */
trait Textable
{
    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $text;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $textEs;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $textFr;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $textIt;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $textZh;

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return self
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextEs(): ?string
    {
        return $this->textEs;
    }

    /**
     * @param string|null $textEs
     *
     * @return self
     */
    public function setTextEs(?string $textEs): self
    {
        $this->textEs = $textEs;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextFr(): ?string
    {
        return $this->textFr;
    }

    /**
     * @param string|null $textFr
     *
     * @return self
     */
    public function setTextFr(?string $textFr): self
    {
        $this->textFr = $textFr;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextIt(): ?string
    {
        return $this->textIt;
    }

    /**
     * @param string|null $textIt
     *
     * @return self
     */
    public function setTextIt(?string $textIt): self
    {
        $this->textIt = $textIt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTextZh(): ?string
    {
        return $this->textZh;
    }

    /**
     * @param string|null $textZh
     *
     * @return self
     */
    public function setTextZh(?string $textZh): self
    {
        $this->textZh = $textZh;

        return $this;
    }
}
