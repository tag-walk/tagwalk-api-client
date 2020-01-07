<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Event;

use LogicException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Security\ApiAuthenticator;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var string
     */
    private $authorizationUrl;

    /**
     * @var string|null
     */
    private $cookieDomain;

    /**
     * @param ApiProvider $apiProvider
     * @param string      $authorizationUrl
     * @param string      $cookieDomain
     */
    public function __construct(
        ApiProvider $apiProvider,
        ?string $authorizationUrl = null,
        ?string $cookieDomain = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->authorizationUrl = $authorizationUrl;
        $this->cookieDomain = $cookieDomain;
    }

    /**
     * This is called when an interactive authentication attempt succeeds. This
     * is called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return Response never null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        if ($this->authorizationUrl === null) {
            throw new LogicException('Authentication without setting authorization_url config is not permitted');
        }
        $session = $request->getSession();
        if ($session === null) {
            throw new RuntimeException('Missing session');
        }
        $queryString = http_build_query($this->apiProvider->getAuthorizationQueryParameters());
        $response = new RedirectResponse(sprintf('%s?%s', $this->authorizationUrl, $queryString));
        $response->headers->setCookie(new Cookie(
            $session->get(ApiAuthenticator::USER_COOKIE_SESSID_NAME),
            $session->get(ApiAuthenticator::USER_COOKIE_SESSID),
            strtotime('+2 minutes'),
            '/',
            $this->cookieDomain
        ));


        return $response;
    }
}
