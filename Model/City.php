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
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;

/**
 * Describe City document.
 *
 * @see Document
 */
class City extends AbstractDocument
{
    use NameTranslatable;

    /**
     * @var bool
     * @Assert\Type("boolean")
     */
    private $main = true;

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     *
     * @return self
     */
    public function setMain(bool $main): self
    {
        $this->main = $main;

        return $this;
    }
}
