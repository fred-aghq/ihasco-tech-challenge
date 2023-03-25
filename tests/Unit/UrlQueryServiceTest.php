<?php

namespace Tests\Unit;

use App\Services\UrlQuery\UrlQueryService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class UrlQueryServiceTest extends TestCase
{
    private string $validProxy;
    private string $invalidProxy;
    private array $validResponseHeaders = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->validProxy = 'https://anyproxy.com:8888';
        $this->invalidProxy = 'notAValidProxy';
        $this->validResponseHeaders = [
            'X-FOO-BAR' => 'baz',
            'X-APPLICATION-HEADER' => 'SomeValue',
        ];
    }

    public function testItFailsIfUrlIsInvalid(): void
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->never();
        });

        $unit = new UrlQueryService($clientMock);

        $this->expectException(ValidationException::class);
        $unit->query('invalid-url', $this->validProxy);
    }

    public function testItFailsIfUrlIsEmptyString(): void
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->never();
        });

        $unit = new UrlQueryService($clientMock);

        $this->expectException(ValidationException::class);
        $unit->query('', $this->validProxy);
    }

    public function testItWorksWithAProxy()
    {
        $mockHandler = new MockHandler([
            new Response(200, $this->validResponseHeaders, '')
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $clientMock = new Client([
            'handler' => $handlerStack,
            'proxy' => $this->validProxy,
            'http_errors' => true,
        ]);

        $unit = new UrlQueryService($clientMock);

        $result = $unit->query('https://example.com', $this->validProxy);
        $this->assertEquals(2, count($result));
    }

    public function testItWorksWithoutAProxy()
    {
        $mockHandler = new MockHandler([
            new Response(200, $this->validResponseHeaders, '')
        ]);

        $handlerStack = HandlerStack::create($mockHandler);

        $clientMock = new Client([
            'handler' => $handlerStack,
            'proxy' => $this->validProxy,
            'http_errors' => true,
        ]);

        $unit = new UrlQueryService($clientMock);

        $result = $unit->query('https://example.com', $this->validProxy);
        $this->assertEquals(2, count($result));
    }
}
