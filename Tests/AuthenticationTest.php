<?php
/** @noinspection PhpParamsInspection */

/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;
use Tagwalk\ApiClientBundle\Security\ApiTokenAuthenticator;

/**
 * Functional test of oauth2 authentication workflows
 *
 * - [x] client_credentials
 * - [ ] authorization_code
 * - [ ] refresh_token
 */
class AuthenticationTest extends TestCase
{
    /**
     * Get oauth2 token via client_credentials authentication method (anonymous)
     */
    public function testAuthenticate(): void
    {
        $ready = isset($_ENV['TAGWALK_API_URL'], $_ENV['TAGWALK_API_CLIENT_ID'], $_ENV['TAGWALK_API_CLIENT_SECRET']);
        if ($ready === false) {
            $this->markTestSkipped('Missing env var to perform functional test. required: TAGWALK_API_URL, TAGWALK_API_CLIENT_ID, TAGWALK_API_CLIENT_SECRET');
        }
        $clientFactory = new ClientFactory($_ENV['TAGWALK_API_URL'], 10, false);
        $apiTokenAuthenticator = new ApiTokenAuthenticator(
            $clientFactory,
            $this->createMock(SessionInterface::class),
            $_ENV['TAGWALK_API_CLIENT_ID'],
            $_ENV['TAGWALK_API_CLIENT_SECRET']
        );
        $result = $apiTokenAuthenticator->authenticate();
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('expires_in', $result);
    }
}
