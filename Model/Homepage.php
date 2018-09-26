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
use Tagwalk\ApiClientBundle\Model\Traits\Programmable;
use Tagwalk\ApiClientBundle\Utils\Constants\HomepageSection;

class Homepage extends AbstractDocument
{
    use Programmable;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice(HomepageSection::VALUES)
     */
    private $section;

    /**
     * @var HomepageCell[]|null
     * @Assert\Valid()
     * @Assert\Type("array")
     */
    private $cells;

    /**
     * @param string $section
     */
    public function __construct(string $section)
    {
        $this->section = $section;
    }

    /**
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * @param string $section
     *
     * @return Homepage
     */
    public function setSection(string $section): Homepage
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return null|HomepageCell[]
     */
    public function getCells(): ?array
    {
        return $this->cells;
    }

    /**
     * @param null|HomepageCell[] $cells
     *
     * @return Homepage
     */
    public function setCells(?array $cells): Homepage
    {
        $this->cells = $cells;

        return $this;
    }

    /**
     * @param HomepageCell $cell
     */
    public function addCell(HomepageCell $cell): void
    {
        $this->cells[] = $cell;
    }

    /**
     * @param HomepageCell $cell
     */
    public function removeCell(HomepageCell $cell): void
    {
        foreach ($this->cells as $index => $currentCell) {
            if ($currentCell->getSlug() === $cell->getSlug()) {
                unset($this->cells[$index]);
            }
        }
    }
}
