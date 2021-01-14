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

use GuzzleHttp\Client;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;
use Tagwalk\ApiClientBundle\Security\ApiTokenAuthenticator;

class ApiTokenAuthenticatorTest extends TestCase
{
    public function testAuthenticateTriggerClientRequest(): void
    {
        $resultSet = ['foo' => 'bar'];
        $response = $this->getResponseMockWithOneCallToGetBody($resultSet);
        $client = $this->getClientWithOneCallToRequest($response, $this->equalTo('POST'), $this->equalTo('/oauth/v2/token'), $this->anything());
        $clientFactory = $this->getClientFactoryWithOneCallToGet($client);
        $session = $this->createMock(SessionInterface::class);
        $authenticator = new ApiTokenAuthenticator($clientFactory, $session, '', '');
        $authentication = $authenticator->authenticate();
        $this->assertEquals($resultSet, $authentication);
    }

    /**
     * @param array $resultSet
     *
     * @return MockObject|ResponseInterface
     */
    private function getResponseMockWithOneCallToGetBody(array $resultSet): MockObject
    {
        $mock = $this->createMock(ResponseInterface::class);
        $mock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn(json_encode($resultSet));

        return $mock;
    }

    /**
     * @param MockObject   $response
     * @param Constraint[] $with
     *
     * @return MockObject|ClientFactory
     */
    private function getClientWithOneCallToRequest(MockObject $response, ...$with): MockObject
    {
        $mock = $this->createMock(Client::class);
        $mock
            ->expects($this->once())
            ->method('request')
            ->with(...$with)
            ->willReturn($response);

        return $mock;
    }

    /**
     * @param MockObject $client
     *
     * @return MockObject|ClientFactory
     */
    private function getClientFactoryWithOneCallToGet(MockObject $client): MockObject
    {
        $mock = $this->createMock(ClientFactory::class);
        $mock
            ->expects($this->once())
            ->method('get')
            ->willReturn($client);

        return $mock;
    }

    /**
     * @dataProvider userTokenProvider
     *
     * @param string|null $userToken
     */
    public function testGetAuthorizationQueryParameters(?string $userToken): void
    {
        $clientFactory = $this->createMock(ClientFactory::class);
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
                ->method('set')
                ->with($this->equalTo(ApiTokenAuthenticator::AUTHORIZATION_STATE), $this->anything());
        $authenticator = new ApiTokenAuthenticator(
            $clientFactory,
            $session,
            'identifier',
            'secret',
            false,
            'redirect_uri'
        );
        $authenticator->setApplicationName('levis');
        $queryParameters = $authenticator->getAuthorizationQueryParameters($userToken);
        $this->assertArrayHasKey('state', $queryParameters);
        $this->assertIsString($queryParameters['state']);
        unset($queryParameters['state']);
        $this->assertEquals(array_filter([
            'response_type'            => 'code',
            'client_id'                => 'identifier',
            'redirect_uri'             => 'redirect_uri',
            'x-auth-token'             => $userToken,
            'tagwalk-application-name' => 'levis',
            'authenticate-in-showroom' => false,
        ]), $queryParameters);
    }

    public function userTokenProvider(): array
    {
        return [
            [null],
            ['foo'],
        ];
    }

    public function testAuthorizeWithWrongState(): void
    {
        $resultSet = ['state' => 'foo'];
        $response = $this->getResponseMockWithOneCallToGetBody($resultSet);
        $client = $this->getClientWithOneCallToRequest($response, $this->equalTo('POST'), $this->equalTo('/oauth/v2/token'), $this->anything());
        $clientFactory = $this->getClientFactoryWithOneCallToGet($client);
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
                ->method('get')
                ->with($this->equalTo(ApiTokenAuthenticator::AUTHORIZATION_STATE))
                ->willReturn('bar');
        $authenticator = new ApiTokenAuthenticator($clientFactory, $session, '', '');
        $this->expectException(InvalidArgumentException::class);
        $authenticator->authorize('', '');
    }
}
