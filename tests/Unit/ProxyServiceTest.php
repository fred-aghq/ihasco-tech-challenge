<?php

namespace Tests\Unit;
use PHPUnit\Framework\TestCase;

class ProxyServiceTest extends TestCase
{
    private ProxyService $unit;

    public function setUp(): void
    {
        $this->unit = new ProxyService();
    }

    public function testItReturnsProxiesForCorrectlyConfiguredUrl()
    {
        $this->assertTrue(false, "test not yet implemented");
    }

    public function testItThrowsExceptionForIncorrectlyConfiguredUrl()
    {
        $this->assertTrue(false, "test not yet implemented");
    }

    public function testItReturnsEmptyArrayIfNoResults()
    {
        $this->assertTrue(false, "test not yet implemented");
    }
}
