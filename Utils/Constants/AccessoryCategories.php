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
 * This class purpose is to list all available media types
 */
final class AccessoryCategories extends Constants
{
    const SHOES = 'shoes';
    const BAGS = 'bags';
    const BELT = 'belt';
    const JEWELLERY = 'jewellery';
    const SUNGLASSES = 'sunglasses';
    const SCARVES = 'scarves';
    const GLOVES = 'gloves';
    const HATS = 'hats';
    const SOCKS = 'socks';

    const VALUES = [
        self::SHOES,
        self::BAGS,
        self::BELT,
        self::JEWELLERY,
        self::SUNGLASSES,
        self::SCARVES,
        self::GLOVES,
        self::HATS,
        self::SOCKS
    ];
}
