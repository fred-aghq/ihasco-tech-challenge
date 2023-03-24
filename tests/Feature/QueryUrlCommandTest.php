<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueryUrlCommandTest extends TestCase
{
    public function testItRunsSuccessfullyWithCorrectInput()
    {
        $this->artisan('query:url https://www.example.com')->assertSuccessful();
    }

    public function testItFailsGracefullyIfUrlMissing()
    {
        $this->artisan('query:url')->assertFailed();
    }

    public function testItFailsGracefullyIfUrlInvalid()
    {
        $this->artisan('query:url obviouslyNotAURL')->assertFailed();
    }
}
