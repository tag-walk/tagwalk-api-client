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

namespace Tagwalk\ApiClientBundle\Utils\Constants;

/**
 * This class purpose is to list all available statuses in constants.
 */
class Status extends Constants
{
    /** @var string disabled resources */
    const DISABLED = 'disabled';

    /** @var string enabled resources */
    const ENABLED = 'enabled';

    /** @var string all resources */
    const ALL = 'all';

    /** @var array */
    const VALUES = [
        self::DISABLED,
        self::ENABLED,
        self::ALL
    ];
}
