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
 * This class purpose is to list all available media types.
 */
final class MediaType extends Constants
{
    /** @var string womenswear */
    const WOMENSWEAR = 'woman';

    /** @var string menswear */
    const MENSWEAR = 'man';

    /** @var string women accessories */
    const ACCESSORIES_WOMEN = 'accessory';

    /** @var string men accessories */
    const ACCESSORIES_MEN = 'accessory-man';

    /** @var string men accessories */
    const COUTURE = 'couture';

    /** @var array values */
    const VALUES = [
        self::WOMENSWEAR,
        self::MENSWEAR,
        self::ACCESSORIES_WOMEN,
        self::ACCESSORIES_MEN,
        self::COUTURE,
    ];
}
