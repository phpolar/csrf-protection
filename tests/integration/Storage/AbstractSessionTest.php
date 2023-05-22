<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use Generator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class AbstractSessionTest extends TestCase
{
    public function setUp(): void
    {
        session_start();
    }

    public function tearDown(): void
    {
        session_unset();
        session_destroy();
    }

    public static function scenarios(): Generator
    {
        yield [uniqid(), uniqid()];
    }

    #[TestDox("Shall allow for setting real session array")]
    #[DataProvider("scenarios")]
    public function testa(string $givenKey, string $givenValue)
    {
        $sut = new class ($_SESSION) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return session_status() !== PHP_SESSION_ACTIVE;
            }
        };
        $sut[$givenKey] = $givenValue;
        $this->assertArrayHasKey($givenKey, $_SESSION);
        $this->assertContains($givenValue, $_SESSION);
    }
}
