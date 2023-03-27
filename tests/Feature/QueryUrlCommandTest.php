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
use Illuminate\Support\Facades\Artisan;
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
        Log::shouldReceive('channel->info')
            ->once()
            ->withArgs(function ($message) {
                $expected = 'https://www.example.com';

                return $message === $expected;
            })
            ->andReturnNull();

        $mockProxyService = $this->mock(ProxyService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProxyList')
                ->once()
                ->andReturn([
                    'https://exampleproxy1.com:8888',
                ]);
        });

        $mockQueryService = $this->mock(UrlQueryService::class, function (MockInterface $mock) {
            $mock->shouldReceive('query')
                ->once()
                ->withArgs(function ($url, $proxy) {
                    return $url === 'https://www.example.com' && $proxy === 'https://exampleproxy1.com:8888';
                })
                ->andReturn([
                        'X-FOO-BAR' => ['baz'],
                        'X-FOO-BAZ' => ['bar'],
                ]);
        });

        // run command
        $this
            ->artisan('query:url https://www.example.com')
            ->expectsOutput('X-FOO-BAR: baz')
            ->expectsOutput('X-FOO-BAZ: bar')
            ->assertSuccessful();
    }

    public function testItAsksForUrlIfNotPassedAsArg()
    {
        Log::shouldReceive('channel->info')
            ->once()
            ->withArgs(function ($message) {
                $expected = 'https://www.example.com';

                return $message === $expected;
            });

        $mockProxyService = $this->mock(ProxyService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getProxyList')
                ->once()
                ->andReturn([
                    'https://exampleproxy1.com:8888',
                ]);
        });

        $mockQueryService = $this->mock(UrlQueryService::class, function (MockInterface $mock) {
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

        $this
            ->artisan('query:url')
            ->expectsQuestion('Enter URL to query', 'https://www.example.com')
            ->assertSuccessful();
    }

    public function testItFailsGracefullyIfUrlInvalid()
    {
        $this->artisan('query:url obviouslyNotAURL')->assertFailed();
    }
}
