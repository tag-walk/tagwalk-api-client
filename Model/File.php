<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Model
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

/**
 * Describe a File document
 */
class File extends AbstractDocument
{
    use Positionable;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $path;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $mimetype;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $extension;

    /**
     * @var string
     * @Assert\Type("string")
     * @Assert\Choice(callback={"Tagwalk\ApiClientBundle\Utils\Constants\DisplayMode", "getAllowedValues"})
     */
    protected $display;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\Choice(callback={"Tagwalk\ApiClientBundle\Utils\Constants\Crop", "getAllowedValues"})
     */
    protected $crop;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $courtesy;

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return File
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     *
     * @return File
     */
    public function setMimetype(string $mimetype): self
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * @return string
     */
    public function getDisplay(): string
    {
        return $this->display;
    }

    /**
     * @param string $display
     *
     * @return File
     */
    public function setDisplay(string $display): self
    {
        $this->display = $display;

        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return self
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyname()
    {
        return sprintf("%s.%s", $this->slug, $this->extension);
    }

    /**
     * @return null|string
     */
    public function getCrop(): ?string
    {
        return $this->crop;
    }

    /**
     * @param null|string $crop
     *
     * @return self
     */
    public function setCrop(?string $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    /**
     * @return string
     */
    public function getCourtesy(): ?string
    {
        return $this->courtesy;
    }

    /**
     * @param string $courtesy
     *
     * @return self
     */
    public function setCourtesy(?string $courtesy): self
    {
        $this->courtesy = $courtesy;

        return $this;
    }
}
