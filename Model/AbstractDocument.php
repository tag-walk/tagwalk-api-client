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

use Tagwalk\ApiClientBundle\Model\Traits\Nameable;
use Tagwalk\ApiClientBundle\Model\Traits\Sluggable;
use Tagwalk\ApiClientBundle\Model\Traits\Statusable;
use Tagwalk\ApiClientBundle\Model\Traits\Timestampable;

abstract class AbstractDocument implements Document
{
    use Sluggable;
    use Statusable;
    use Timestampable;
    use Nameable;
}
