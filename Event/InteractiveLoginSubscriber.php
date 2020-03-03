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

namespace Tagwalk\ApiClientBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class InteractiveLoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var ApiTokenStorage
     */
    private $apiTokenStorage;

    /**
     * @param SessionInterface $session
     * @param ApiTokenStorage  $apiTokenStorage
     */
    public function __construct(SessionInterface $session, ApiTokenStorage $apiTokenStorage)
    {
        $this->session = $session;
        $this->apiTokenStorage = $apiTokenStorage;
    }

    /**
     * {@inheritdoc}
     *
     * @uses setUserLocale
     * @uses initTokenStorage
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                ['setUserLocale', 0],
                ['initTokenStorage', 0],
            ],
        ];
    }

    /**
     * Set user locale in session to be used by JMS\I18nRoutingBundle\Router::DefaultLocaleResolver
     *
     * @param InteractiveLoginEvent $event
     */
    public function setUserLocale(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (null !== $user->getLocale()) {
            $this->session->set('_locale', $user->getLocale());
        }
    }

    /**
     * Init api token storage with token username
     */
    public function initTokenStorage(): void
    {
        $this->apiTokenStorage->init();
    }
}
