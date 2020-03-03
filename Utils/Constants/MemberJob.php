<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Utils\Constants;

class MemberJob
{
    const CREATIVE_DIRECTOR = 'creative_director';
    const STYLIST = 'stylist';
    const HAIR_STYLIST = 'hair_stylist';
    const MAKEUP_ARTIST = 'makeup_artist';
    const NAILS = 'nails';
    const CASTING_DIRECTOR = 'casting_director';
    const MUSIC = 'music';
    const PRODUCTION = 'production';
    const COLLABORATION = 'collaboration';

    const VALUES = [
        self::CREATIVE_DIRECTOR,
        self::STYLIST,
        self::HAIR_STYLIST,
        self::MAKEUP_ARTIST,
        self::NAILS,
        self::CASTING_DIRECTOR,
        self::MUSIC,
        self::PRODUCTION,
        self::COLLABORATION,
    ];
}
