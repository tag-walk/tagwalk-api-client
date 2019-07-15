<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Florian Ajir <florian@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Describe Cover document.
 *
 * @see Document
 */
class Cover extends AbstractDocument
{
    /**
     * @var File
     * @Assert\Valid()
     * @Assert\Type("object")
     */
    private $file;

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }
}
