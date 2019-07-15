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
 * This class purpose is to list all available tag talk types.
 */
final class TalkCategories extends Constants
{
    const STYLISTS = 'stylists';
    const DESIGNERS = 'designers';
    const INFLUENCERS = 'influencers';
    const PHOTOGRAPHERS = 'photographers';
    const JOURNALISTS = 'journalists';
    const MODELS = 'models';
    const CURATORS = 'curators';
    const DIGITAL = 'digital';
    const ARTISTS = 'artists';
    const BUSINESS = 'business';

    const VALUES = [
        self::STYLISTS,
        self::DESIGNERS,
        self::INFLUENCERS,
        self::PHOTOGRAPHERS,
        self::JOURNALISTS,
        self::MODELS,
        self::CURATORS,
        self::DIGITAL,
        self::ARTISTS,
        self::BUSINESS,
    ];
}
