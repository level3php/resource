<?php
namespace Level3\Tests\Processor\Wrapper;

use Level3\Tests\TestCase;
use Level3\Processor\Wrapper\Authenticator\Credentials;

class CredentialsTest extends TestCase
{
    private $wrapper;

    public function testConstruct()
    {
        $credentials = new Credentials(true);
        $this->assertTrue($credentials->isAuthenticated());
    }

    public function testToString()
    {
        $credentials = new Credentials(true);
        $this->assertSame('Authenticated: true', (string) $credentials);
    }
}
