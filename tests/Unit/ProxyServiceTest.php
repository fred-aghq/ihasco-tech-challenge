<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Services\Proxy\ProxyService;

class ProxyServiceTest extends TestCase
{
    private string $validUri = '';

    public function setUp(): void
    {
        parent::setUp();

        $this->validUri = 'https://example-proxy-api.com/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=yes&anonymity=all';
    }

    public function testItThrowsExceptionIfNoUrlConfigured()
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->never();
        });

        $this->expectException(\Exception::class);

        $unit = new ProxyService($clientMock, null);
        $unit->getListOfProxies();
    }

    public function testItReturnsProxiesForCorrectlyConfiguredUrl()
    {
        $validUri = $this->validUri;

        $clientMock = $this->mock(Client::class, function (MockInterface $mock) use ($validUri) {
            $mock->shouldReceive('get')
                ->with($this->validUri)
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

        $unit = new ProxyService($clientMock, $this->validUri);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 3);
    }

    public function testItReturnsEmptyArrayIfNoResults()
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->with($this->validUri)
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        [],
                        json_encode([])
                    )
                );
        });

        $unit = new ProxyService($clientMock, $this->validUri);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 0);
    }
}
