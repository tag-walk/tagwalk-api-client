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
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]]
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $requestLocale = $request->attributes->get('_locale');
        $session = $request->getSession();
        if ($requestLocale) {
            if ($session) {
                $session->set('_locale', $requestLocale);
            }
        } else {
            // if no explicit locale has been set on this request:
            // 1: use one from the session
            // 2: use prefered from browser config
            // 3: use application default locale
            $sessionLocale = $session ? $session->get('_locale') : null;
            $request->setLocale(
                $sessionLocale
                ?? $request->getPreferredLanguage(array_values(Language::getAllowedValues()))
                ?? $this->defaultLocale
            );
        }
    }
}
