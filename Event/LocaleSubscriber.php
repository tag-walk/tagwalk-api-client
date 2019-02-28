<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @package     App\Event
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tagwalk\ApiClientBundle\Utils\Constants\Language;

/**
 * Handle kernel request to determine the locale from headers
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param string $defaultLocale
     */
    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // if no explicit locale has been set on this request:
            // 1: use one from the session
            // 2: use prefered from browser config
            // 3: use application default locale
            $request->setLocale(
                $request->getSession()->get('_locale')
                ?? $request->getPreferredLanguage(array_values(Language::getAllowedValues()))
                ?? $this->defaultLocale
            );
        }
    }
}
