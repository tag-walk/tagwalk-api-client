<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Security;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ApiAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiProvider
     */
    private $provider;

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param ApiProvider $provider
     * @param SerializerInterface $serializer
     * @param SessionInterface $session
     */
    public function __construct(ApiProvider $provider, SerializerInterface $serializer, SessionInterface $session)
    {
        $this->provider = $provider;
        $this->serializer = $serializer;
        $this->session = $session;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        // GOOD behavior: only authenticate (i.e. return true) on a specific route
        return 'login' === $request->attributes->get('_route') && $request->isMethod('POST');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * @param Request $request
     * @return array
     */
    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $password = $credentials['password'];
        $email = $credentials['username'];
        if (isset($password) && isset($email)) {
            try {
                $response = $this->provider->request('POST', '/api/users/login', [
                    RequestOptions::JSON => [
                        'email' => $email,
                        'password' => $password
                    ]
                ]);
                $json = $response->getBody()->getContents();
                $sessid = explode(';', $response->getHeaderLine('Set-Cookie'));
                $this->session->set('Cookie', $sessid[0]);

                return $this->serializer->deserialize($json, User::class, 'json');
            } catch (GuzzleException $exception) {
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}