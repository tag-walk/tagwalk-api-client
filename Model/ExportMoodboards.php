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

class ExportMoodboards extends Export
{
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
