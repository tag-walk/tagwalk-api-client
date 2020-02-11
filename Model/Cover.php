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

use Tagwalk\ApiClientBundle\Model\Traits\Fileable;

/**
 * Describe Cover document.
 *
 * @see Document
 */
class Cover extends AbstractDocument
{
    use Fileable;
}
