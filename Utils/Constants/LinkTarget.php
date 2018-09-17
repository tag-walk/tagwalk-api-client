<?php declare(strict_types=1);
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     Tagwalk\ApiClientBundle\Utils\Constants
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

final class LinkTarget extends Constants
{
    const BLANK = '_blank';
    const PARENT = '_parent';
    const SELF = '_self';
    const TOP = '_top';

    const VALUES = [
        self::BLANK,
        self::PARENT,
        self::SELF,
        self::TOP
    ];
}
