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

namespace Tagwalk\ApiClientBundle\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public service to get RedirectResponse to api authorization url
 */
class AuthorizationHelper
{
    /**
     * @var ApiTokenAuthenticator
     */
    private $authenticator;

    /**
     * @var string|null
     */
    private $authorizationUrl;

    /**
     * @param ApiTokenAuthenticator $authenticator
     * @param string|null           $authorizationUrl
     */
    public function __construct(
        ApiTokenAuthenticator $authenticator,
        ?string $authorizationUrl = null
    ) {
        $this->authenticator = $authenticator;
        $this->authorizationUrl = $authorizationUrl;
    }

    /**
     * Get RedirectResponse to api authorization url with right parameters
     *
     * @param string|null $userToken
     *
     * @return RedirectResponse
     */
    public function getRedirect(?string $userToken): RedirectResponse
    {
        if (null === $this->authorizationUrl) {
            throw new NotFoundHttpException('Missing authorization url configuration');
        }
        $queryParams = $this->authenticator->getAuthorizationQueryParameters($userToken);
        $queryString = http_build_query($queryParams);
        $url = sprintf('%s?%s', $this->authorizationUrl, $queryString);

        return new RedirectResponse($url);
    }
}
