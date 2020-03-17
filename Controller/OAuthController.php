<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Controller;

use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Security\ApiTokenAuthenticator;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

/**
 * @Route("/oauth2")
 */
class OAuthController extends AbstractController
{
    /**
     * @var ApiTokenAuthenticator
     */
    private $apiTokenAuthenticator;

    /**
     * @var ApiTokenStorage
     */
    private $apiTokenStorage;

    /**
     * @param ApiTokenAuthenticator $apiTokenAuthenticator
     * @param ApiTokenStorage       $apiTokenStorage
     */
    public function __construct(ApiTokenAuthenticator $apiTokenAuthenticator, ApiTokenStorage $apiTokenStorage)
    {
        $this->apiTokenAuthenticator = $apiTokenAuthenticator;
        $this->apiTokenStorage = $apiTokenStorage;
    }

    /**
     * @Route("/authorize", name="oauth2_authorize", options={"i18n"=false})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function authorize(Request $request): Response
    {
        if (false === $request->query->has('code')) {
            throw $this->createAccessDeniedException('missing authorization code');
        }
        /** @var User $user */
        $user = $this->getUser();
        if (null === $user || false === $user instanceof User || null === $userToken = $user->getApiToken()) {
            throw $this->createAccessDeniedException('missing user authentication');
        }
        $code = $request->query->get('code');
        $escaped = urldecode($code);

        try {
            $authentication = $this->apiTokenAuthenticator->authorize($escaped, $userToken);
            $this->apiTokenStorage->setAccessToken(
                $authentication['access_token'] ?? null,
                $authentication['expires_in'] ?? ApiTokenStorage::DEFAULT_ACCESS_TOKEN_TTL
            );
            $this->apiTokenStorage->setRefreshToken($authentication['refresh_token'] ?? null);
        } catch (ClientException $exception) {
            throw new BadRequestHttpException($exception->getMessage());
        }
        // redirection
        $session = $request->getSession();
        $redirect = '/';
        if ($session) {
            $redirect = $session->get('_security.main.target_path') ?? $session->get('login_redirect');
        }

        return new RedirectResponse($redirect);
    }
}
