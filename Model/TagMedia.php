<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author Vincent Duruflé <vincent@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license proprietary
 */

namespace Tagwalk\ApiClientBundle\Model;

use Tagwalk\ApiClientBundle\Model\Traits\Coverable;
use Tagwalk\ApiClientBundle\Model\Traits\Mediable;
use Tagwalk\ApiClientBundle\Model\Traits\NameTranslatable;
use Tagwalk\ApiClientBundle\Model\Traits\Positionable;

/**
 * Used in trends documents
 * Extended Tag document which contains medias
 */
class TagMedia extends AbstractDocument
{
    use Coverable;
    use NameTranslatable;
    use Positionable;
    use Mediable;
}
