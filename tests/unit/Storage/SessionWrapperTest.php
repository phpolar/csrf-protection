<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SessionWrapper::class)]
#[UsesClass(AbstractSession::class)]
final class SessionWrapperTest extends TestCase
{
    protected function setUp(): void
    {
        session_start();
    }

    protected function tearDown(): void
    {
        session_unset();
        session_status() === PHP_SESSION_ACTIVE && session_destroy();
    }

    #[TestDox("Shall determine if the session is not active")]
    public function testa()
    {
        session_destroy();
        $fakeSessionVars = [];
        $sut = new SessionWrapper($fakeSessionVars);
        $result = $sut->isNotActive();
        $this->assertTrue($result);
    }

    #[TestDox("Shall determine if the session is active")]
    public function testb()
    {
        $fakeSessionVars = [];
        $sut = new SessionWrapper($fakeSessionVars);
        $result = $sut->isNotActive();
        $this->assertFalse($result);
    }
}
