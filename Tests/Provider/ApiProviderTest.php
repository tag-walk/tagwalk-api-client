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

namespace Tagwalk\ApiClientBundle\Tests\Provider;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ApiProviderTest extends TestCase
{

    private function getNewApiProvider($baseUri = '', $clientId = '', $clientSecret = '', $timeout = 0)
    {
        $request = $this->createMock(Request::class);
        $request->method('getLocale')->willReturn('en');
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        /** @var RequestStack $requestStack */

        return new ApiProvider($requestStack, $baseUri, $clientId, $clientSecret, $timeout);
    }

    public function testConstructor()
    {
        $provider = $this->getNewApiProvider();
        $this->assertNotNull($provider);
        $this->assertInstanceOf('Tagwalk\ApiClientBundle\Provider\ApiProvider', $provider);
    }

    public function testInitBaseUri()
    {
        $baseUri = 'https://api.tag-walk.com';
        $provider = $this->getNewApiProvider($baseUri);
        $baseUri = $provider->getClient()->getConfig('base_uri');
        $this->assertEquals('https://api.tag-walk.com', $baseUri);
    }

    public function testInitTimeout()
    {
        $provider = $this->getNewApiProvider('', '', '', 42);
        $timeout = $provider->getClient()->getConfig('timeout');
        $this->assertEquals(42, $timeout);
    }
}