<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Utils
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

/**
 * This class purpose is to list all available statuses in constants
 */
final class Status extends Constants
{
    /** @var string disabled ressource */
    const DISABLED = 'disabled';

    /** @var string enabled ressource */
    const ENABLED = 'enabled';

    /** @var array */
    const VALUES = [
        self::DISABLED,
        self::ENABLED
    ];
}
