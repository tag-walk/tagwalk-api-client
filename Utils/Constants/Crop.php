<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Utils
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

/**
 * This class purpose is to list all available crop values
 */
final class Crop extends Constants
{
    const TOP = 't';
    const LEFT = 'l';
    const RIGHT = 'r';
    const BOTTOM = 'b';
    const BOTTOM_LEFT = 'bl';
    const BOTTOM_RIGHT = 'br';
    const TOP_LEFT = 'tl';
    const TOP_RIGHT = 'tr';

    const VALUES = [
        self::TOP,
        self::LEFT,
        self::RIGHT,
        self::BOTTOM,
        self::BOTTOM_LEFT,
        self::BOTTOM_RIGHT,
        self::TOP_LEFT,
        self::TOP_RIGHT,
    ];
}
