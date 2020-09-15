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
class UserStatus extends Status
{
    /** @var string pending moderation account */
    const PENDING = 'pending';

    /** @var string disabled account */
    const DISABLED = 'disabled';

    /** @var string enabled account */
    const ENABLED = 'enabled';

    /** @var array */
    const VALUES = [
        self::DISABLED,
        self::PENDING,
        self::ENABLED,
    ];
}
