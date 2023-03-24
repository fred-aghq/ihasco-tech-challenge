<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Services\Proxy\ProxyService;

class ProxyServiceTest extends TestCase
{

    public function testItReturnsProxiesForCorrectlyConfiguredUrl()
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->once()
                ->andReturn(
                    new Response(200, [], json_encode([
                            'https://example-proxy.com',
                            'https://example-proxy2.com',
                            'https://example-proxy3.com',
                        ])
                    )
                );
        });

        $unit = new ProxyService($clientMock);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 3);
    }

    public function testItReturnsEmptyArrayIfNoResults()
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->with('/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=yes&anonymity=all')
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        [],
                        json_encode([])
                    )
                );
        });

        $unit = new ProxyService($clientMock);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 0);
    }
}
