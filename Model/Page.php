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

use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;
use Tagwalk\ApiClientBundle\Utils\Constants\PageSection;

/**
 * Model for static pages documents.
 */
class Page extends AbstractDocument
{
    use Textable,
        Positionable,
        NameTranslatable;

    /**
     * The section is where to put a link to the Page (e.g. the header)
     * @see PageSection
     */
    private ?string $section = null;

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(?string $section): self
    {
        $this->section = $section;

        return $this;
    }
}
