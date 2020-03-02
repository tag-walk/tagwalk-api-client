<?php
/**
 * PHP version 7.
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Security\ApiAuthenticator;
use Tagwalk\ApiClientBundle\Security\AuthorizationHelper;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var AuthorizationHelper
     */
    private $authorizationHelper;

    /**
     * @param AuthorizationHelper $authorizationHelper
     */
    public function __construct(AuthorizationHelper $authorizationHelper)
    {
        $this->authorizationHelper = $authorizationHelper;
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
        $session = $request->getSession();
        if ($session === null) {
            throw new RuntimeException('Missing session');
        }
        /** @var User $user */
        $user = $token->getUser();
        if (null === $user) {
            throw new RuntimeException('Missing user in token');
        }

        return $this->authorizationHelper->getRedirect($user->getApiToken());
    }
}
