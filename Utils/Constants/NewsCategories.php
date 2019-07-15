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
 * This class purpose is to list all available tag news types.
 */
final class NewsCategories extends Constants
{
    const COLLABORATION = 'collaboration';
    const INFLUENCERS = 'influencers';
    const CAMPAIGN = 'campaign';
    const CULTURE = 'culture';
    const NEW_PRODUCTS = 'new_products';
    const EVENTS = 'events';
    const FASHION_HOUSE = 'fashion_house';
    const BREAKING_NEWS = 'breaking_news';
    const VALUES = [
        self::COLLABORATION,
        self::INFLUENCERS,
        self::CAMPAIGN,
        self::CULTURE,
        self::NEW_PRODUCTS,
        self::EVENTS,
        self::FASHION_HOUSE,
        self::BREAKING_NEWS,
    ];
}
