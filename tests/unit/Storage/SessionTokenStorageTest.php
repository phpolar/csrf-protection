<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use DateTimeImmutable;
use Generator;
use Phpolar\CsrfProtection\CsrfToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SessionTokenStorage::class)]
#[UsesClass(AbstractSession::class)]
#[UsesClass(AbstractTokenStorage::class)]
#[UsesClass(CsrfToken::class)]
final class SessionTokenStorageTest extends TestCase
{
    public static function activeSessions(): Generator
    {
        $requestId = uniqid();
        $fakeSession = [$requestId => [new CsrfToken(new DateTimeImmutable("now"))]];
        $activeSession = new class ($fakeSession) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return false;
            }
        };
        yield [$activeSession, $fakeSession, $requestId];
    }

    public static function activeSessionsWithGivenToken(): Generator
    {
        $requestId = uniqid();
        $givenToken = new CsrfToken(new DateTimeImmutable("now"));
        $fakeSession = [$requestId => [$givenToken]];
        $activeSession = new class ($fakeSession) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return false;
            }
        };
        yield [$activeSession, $fakeSession, $requestId, $givenToken];
    }

    public static function activeSessionsWithoutTokens(): Generator
    {
        $requestId = uniqid();
        $fakeEmptySession = [];
        $activeSession = new class ($fakeEmptySession) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return false;
            }
        };
        yield [$activeSession, $requestId];
    }

    public static function inactiveSessionsWithTokens(): Generator
    {
        $requestId = uniqid();
        $fakeSession = [$requestId => [new CsrfToken(new DateTimeImmutable("now"))]];
        $activeSession = new class ($fakeSession) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return true;
            }
        };
        yield [$activeSession, $requestId];
    }

    #[TestDox("Shall load from active session when created")]
    #[DataProvider("activeSessions")]
    public function testa(AbstractSession $givenSession, array $expectedSessionState, string $requestId)
    {
        $sut = new SessionTokenStorage($givenSession, $requestId);
        for ($i = 0; $i < count($expectedSessionState[$requestId]); ++$i) {
            $this->assertTrue((string) $expectedSessionState[$requestId][$i] === (string) $sut->queryAll()[$i]);
        }
    }

    #[TestDox("Shall not load from inactive session when created")]
    #[DataProvider("inactiveSessionsWithTokens")]
    public function testb(AbstractSession $givenSession, string $requestId)
    {
        $sut = new SessionTokenStorage($givenSession, $requestId);
        $this->assertEmpty($sut->queryAll());
    }

    #[TestDox("Shall not load from active session without tokens when created")]
    #[DataProvider("activeSessionsWithoutTokens")]
    public function testc(AbstractSession $givenSession, string $requestId)
    {
        $sut = new SessionTokenStorage($givenSession, $requestId);
        $this->assertEmpty($sut->queryAll());
    }

    #[TestDox("Shall return null for non-existing token when queried")]
    #[DataProvider("activeSessionsWithoutTokens")]
    public function testd(AbstractSession $givenSession, string $requestId /* required since phpunit 12.4 */)
    {
        $sut = new SessionTokenStorage($givenSession);
        $this->assertNull($sut->queryOne());
    }

    #[TestDox("Shall load from active session when created")]
    #[DataProvider("activeSessionsWithGivenToken")]
    public function teste(AbstractSession $givenSession, array $expectedSessionState, string $requestId, CsrfToken $givenToken)
    {
        $sut = new SessionTokenStorage($givenSession, $requestId);
        $this->assertSame((string) $givenToken, (string) $sut->queryOne());
    }
}
