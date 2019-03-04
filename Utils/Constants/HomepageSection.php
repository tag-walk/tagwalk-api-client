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
 * This class purpose is to list all available homepage.section values
 */
final class HomepageSection extends Constants
{
    /** @var string home section */
    const HOME = 'home';

    /** @var string shop section */
    const SHOP = 'shop';

    /** @var string street section */
    const STREET = 'street';

    /** @var array */
    const VALUES = [
        self::HOME,
        self::SHOP,
        self::STREET
    ];
}
