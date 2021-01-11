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
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

/**
 * Describe a File document.
 */
class File extends AbstractDocument
{
    use Positionable;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $path;

    /**
     * @var array|null
     * @Assert\Type("array")
     */
    protected $variants;

    /**
     * @var string|null
     * @Assert\Type("string")
     * @Assert\NotBlank()
     */
    protected $filename;

    /**
     * @Assert\Type("string")
     */
    protected ?string $originalFilename;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $mimetype;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    protected $extension;

    /**
     * @var string|null
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
     * @var string|null
     * @Assert\Type("string")
     */
    private $caption;

    /**
     * @var bool|null
     */
    private $embed;

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(?string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(?string $mimetype): self
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    public function getDisplay(): ?string
    {
        return $this->display;
    }

    public function setDisplay(?string $display): self
    {
        $this->display = $display;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getKeyname(): string
    {
        return sprintf('%s.%s', $this->slug, $this->extension);
    }

    public function getCrop(): ?string
    {
        return $this->crop;
    }

    public function setCrop(?string $crop): self
    {
        $this->crop = $crop;

        return $this;
    }

    public function getCourtesy(): ?string
    {
        return $this->courtesy;
    }

    public function setCourtesy(?string $courtesy): self
    {
        $this->courtesy = strip_tags($courtesy);

        return $this;
    }

    public function getVariants(): ?array
    {
        return $this->variants;
    }

    public function setVariants(?array $variants): self
    {
        $this->variants = $variants;

        return $this;
    }

    public function getVariant(string $variant): ?string
    {
        return $this->variants[$variant] ?? null;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function isEmbed(): ?bool
    {
        return $this->embed;
    }

    public function setEmbed(?bool $embed): self
    {
        $this->embed = $embed;

        return $this;
    }
}
