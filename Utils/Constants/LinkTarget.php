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
        self::TOP,
    ];
}
