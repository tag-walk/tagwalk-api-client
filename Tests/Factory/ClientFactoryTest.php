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

namespace Tagwalk\ApiClientBundle\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;

class ClientFactoryTest extends TestCase
{
    public function testGetSingletonInstance(): void
    {
        $clientFactory = new ClientFactory();
        self::assertEquals($clientFactory->get(), $clientFactory->get());
    }

    public function testConfig(): void
    {
        $clientFactory = new ClientFactory(
            'https://bar.foo',
            10.0,
            true
        );
        self::assertEquals('https://bar.foo', $clientFactory->get()->getConfig('base_uri'));
        self::assertEquals(10.0, $clientFactory->get()->getConfig('timeout'));
        self::assertNotEmpty($clientFactory->get()->getConfig('handler'));
    }
}
