<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class HomepageCell extends AbstractDocument
{
    use Textable;
    use Linkable;
    use Positionable;

    /**
     * @var int
     * @Assert\Type("int")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(value="0", message="Position must be positive")
     */
    protected $position;

    /**
     * @var File[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $files;

    /**
     * @var int
     * @Assert\Type("int")
     * @Assert\GreaterThan(value="0", message="The width must be greater than zero")
     * @Assert\LessThanOrEqual(value="12", message="The maximum width is 12")
     */
    private $width = 1;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"Tagwalk\ApiClientBundle\Utils\Constants\HomepageCellType", "getAllowedValues"})
     */
    private $type;

    /**
     * @var string[]|null
     * @Assert\Type("array")
     */
    private $filters;

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return HomepageCell
     */
    public function setPosition(int $position): HomepageCell
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return null|File[]
     */
    public function getFiles(): ?array
    {
        return $this->files;
    }

    /**
     * @param null|File[] $files
     *
     * @return HomepageCell
     */
    public function setFiles(?array $files): HomepageCell
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return HomepageCell
     */
    public function setWidth(int $width): HomepageCell
    {
        $this->width = $width;

        return $this;
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
     *
     * @return HomepageCell
     */
    public function setType(string $type): HomepageCell
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return null|string[]
     */
    public function getFilters(): ?array
    {
        return $this->filters;
    }

    /**
     * @param null|string[] $filters
     *
     * @return HomepageCell
     */
    public function setFilters(?array $filters): HomepageCell
    {
        $this->filters = $filters;

        return $this;
    }
}
