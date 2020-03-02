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

use DateInterval;
use DateTime;

class ApiCredentials
{
    /**
     * @var string|null
     */
    private $userToken;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string|null
     */
    private $refreshToken;

    /**
     * @var DateTime
     */
    private $expiration;

    /**
     * @return string|null
     */
    public function getUserToken(): ?string
    {
        return $this->userToken;
    }

    /**
     * @param string|null $userToken
     */
    public function setUserToken(?string $userToken): void
    {
        $this->userToken = $userToken;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return DateTime
     */
    public function getExpiration(): DateTime
    {
        return $this->expiration;
    }

    /**
     * @param DateTime $expiration
     */
    public function setExpiration(DateTime $expiration): void
    {
        $this->expiration = $expiration;
    }

    /**
     * Denormalize api response (array) and returns updated ApiCredentials
     *
     * @param array $response
     *
     * @return self
     */
    public function denormalize(array $response): self
    {
        if (isset($response['access_token'])) {
            $this->setAccessToken($response['access_token']);
        }
        if (isset($response['expires_in'])) {
            $this->setExpiration((new DateTime())->add(new DateInterval(sprintf('PT%dS', $response['expires_in']))));
        }
        $this->setRefreshToken($response['refresh_token'] ?? null);

        return $this;
    }
}
