<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;
use Tests\TestCase;
use App\Services\Proxy\ProxyService;

class ProxyServiceTest extends TestCase
{

    private string $validUri = '';
    private array $validProxyList = [];
    private string $validProxyListRaw = '';

    public function setUp(): void
    {
        parent::setUp();

        $this->validUri = 'https://example-proxy-api.com/v2/?request=displayproxies&protocol=http&timeout=10000&country=all&ssl=yes&anonymity=all';

        // I'd use @dataProvider if this was any more complex
        $this->validProxyList = [
            'https://example-proxy.com',
            'https://example-proxy2.com',
            'https://example-proxy3.com',
        ];

        $this->validProxyListRaw = "https://example-proxy.com\nhttps://example-proxy2.com\nhttps://example-proxy3.com";
    }

    public function testItReturnsProxiesIfResults()
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->with($this->validUri)
                ->once()
                ->andReturn(
                    new Response(200, [], $this->validProxyListRaw
                    )
                );
        });

        $unit = new ProxyService($clientMock, $this->validUri);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 3);
        $this->assertEquals($unitResult, $this->validProxyList);
    }

    public function testItReturnsEmptyArrayIfNoResults()
    {
        $request = new Request('GET', $this->validUri);

        $clientMock = $this->mock(Client::class, function (MockInterface $mock) use ($request) {
            $mock->shouldReceive('get')
                ->with($this->validUri)
                ->once()
                ->andReturn(
                    new Response(
                        200,
                        [],
                        ''
                    )
                );
        });

        $unit = new ProxyService($clientMock, $this->validUri);
        $unitResult = $unit->getListOfProxies();

        $this->assertEquals(count($unitResult), 0);
    }

    public function testItThrowsExceptionIfRequestUnsuccessful()
    {
        $request = new Request('GET', $this->validUri);

        $clientMock = $this->mock(Client::class, function (MockInterface $mock) use ($request) {
            $mock->shouldReceive('get')
                ->with($this->validUri)
                ->once()
                ->andReturn(
                    new Response(
                        500,
                        [],
                        ''
                    )
                );
        });

        $unit = new ProxyService($clientMock, $this->validUri);

        $this->expectException(\Exception::class, 'Problem requesting proxy list, check logs for more info');
        $unitResult = $unit->getListOfProxies();
    }
}
