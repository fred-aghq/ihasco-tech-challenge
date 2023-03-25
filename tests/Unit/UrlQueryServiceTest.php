<?php

namespace Tests\Unit;

use App\Services\UrlQuery\UrlQueryService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Mockery\MockInterface;
use Tests\TestCase;

class UrlQueryServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testItFailsIfUrlIsInvalid(): void
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->never();
        });

        $unit = new UrlQueryService($clientMock);

        $this->expectException(ValidationException::class);
        $unit->query('invalid-url');
    }

    public function testItFailsIfUrlIsEmptyString(): void
    {
        $clientMock = $this->mock(Client::class, function (MockInterface $mock) {
            $mock->shouldReceive('get')
                ->never();
        });

        $unit = new UrlQueryService($clientMock);

        $this->expectException(ValidationException::class);
        $unit->query('');
    }

//    public function testItLogs
}
