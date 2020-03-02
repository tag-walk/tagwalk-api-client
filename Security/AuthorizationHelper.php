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
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class AuthorizationHelper
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var string|null
     */
    private $authorizationUrl;

    /**
     * @param ApiProvider $apiProvider
     * @param string      $authorizationUrl
     */
    public function __construct(ApiProvider $apiProvider, ?string $authorizationUrl = null)
    {
        $this->apiProvider = $apiProvider;
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
        $queryParams = $this->apiProvider->getAuthorizationQueryParameters($userToken);
        $queryString = http_build_query($queryParams);
        $url = sprintf('%s?%s', $this->authorizationUrl, $queryString);

        return new RedirectResponse($url);
    }
}
