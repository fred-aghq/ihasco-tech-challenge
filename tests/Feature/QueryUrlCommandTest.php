<?php

namespace Tests\Feature;

use App\Services\Proxy\ProxyService;
use App\Services\UrlQuery\UrlQueryService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Handler\Proxy;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery\MockInterface;
use Tests\TestCase;

class QueryUrlCommandTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2021-01-01 00:00:00');
    }

    public function testItRunsSuccessfullyWithCorrectInput()
    {
        // prep log facade
        Log::shouldReceive('channel->info')
            ->once()
            ->withArgs(function ($message) {
                $expected = 'https://www.example.com';

                return $message === $expected;
            });

        $mockProxyService = $this->mock(ProxyService::class, function(MockInterface $mock) {
            $mock->shouldReceive('getProxyList')
                ->once()
                ->andReturn([
                    'https://exampleproxy1.com:8888',
                    'https://exampleproxy2.com:8998',
                ]);
        });

        $mockQueryService = $this->mock(UrlQueryService::class, function(MockInterface $mock) {
            $mock->shouldReceive('query')
                ->once()
                ->withArgs(function ($url, $proxy) {
                    return $url === 'https://www.example.com' && $proxy === 'https://exampleproxy1.com:8888';
                })
                ->andReturn([
                    'headers' => [
                        'X-FOO-BAR' => 'baz',
                    ],
                ]);
        });

        // run command
        $this
            ->artisan('query:url https://www.example.com')
            ->assertSuccessful();
    }

    public function testItFailsGracefullyIfUrlMissing()
    {
        $this->expectException(\RuntimeException::class);
        $this->artisan('query:url')->assertFailed();
    }

    public function testItFailsGracefullyIfUrlInvalid()
    {
        $this->artisan('query:url obviouslyNotAURL')->assertFailed();
    }
}
