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
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ApiProviderTest extends TestCase
{
    public function testConstructor()
    {
        $provider = new ApiProvider('', '', '', '');
        $this->assertNotNull($provider);
        $this->assertInstanceOf('Tagwalk\ApiClientBundle\Provider\ApiProvider', $provider);
    }

    public function testInitBaseUri()
    {
        $provider = new ApiProvider('https://api.tag-walk.com', '', '', '');
        $baseUri = $provider->getClient()->getConfig('base_uri');
        $this->assertEquals('https://api.tag-walk.com', $baseUri);

    }

    public function testInitTimeout()
    {
        $provider = new ApiProvider('', '', '', 42);
        $timeout = $provider->getClient()->getConfig('timeout');
        $this->assertEquals(42, $timeout);
    }
}