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

namespace Tagwalk\ApiClientBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Tagwalk\ApiClientBundle\Security\ApiTokenAuthenticator;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

class ApiTokenStorageTest extends TestCase
{
    public function testInitWithoutParamGetUsernameFromTokenStorage(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->expects($this->once())
            ->method('getUsername');
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($token);
        $authenticator = $this->createMock(ApiTokenAuthenticator::class);
        $clientFactory = new ApiTokenStorage($tokenStorage, $authenticator);
        $clientFactory->init();
    }
}
