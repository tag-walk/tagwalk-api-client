<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Linkable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;
use Tagwalk\ApiClientBundle\Model\Traits\Textable;

class Press extends AbstractDocument
{
    use Coverable;
    use Linkable;
    use Positionable;
    use Textable;
}
