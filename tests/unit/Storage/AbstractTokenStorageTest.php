<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use DateTimeImmutable;
use Generator;
use Phpolar\CsrfProtection\CsrfToken;
use PHPUnit\Framework\TestCase;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;

use const Phpolar\CsrfProtection\TOKEN_MAX;

#[CoversClass(AbstractTokenStorage::class)]
#[UsesClass(CsrfToken::class)]
final class AbstractTokenStorageTest extends TestCase
{
    public static function validTokens(): Generator
    {
        foreach (range(1, TOKEN_MAX) as $n) {
            yield [new CsrfToken(new DateTimeImmutable())];
        }
    }

    public static function excessiveTokenCount(): array
    {
        return [
            [
                array_map(
                    static fn () => new CsrfToken(new DateTimeImmutable()),
                    range(1, TOKEN_MAX + 1)
                )
            ],
        ];
    }

    #[TestDox("Shall know if a token is valid")]
    #[DataProvider("validTokens")]
    public function test1(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertTrue($sut->isValid((string) $token));
    }

    #[TestDox("Shall know if a token is invalid")]
    public function test2()
    {
        $sut = new MemoryTokenStorageStub();
        $token = new CsrfToken(new DateTimeImmutable("yesterday"));
        $sut->add($token);
        $this->assertFalse($sut->isValid((string) $token));
    }

    #[TestDox("Shall know if a token is invalid")]
    public function test2b()
    {
        $sut = new MemoryTokenStorageStub();
        $token = new CsrfToken(new DateTimeImmutable());
        $sut->add(new CsrfToken(new DateTimeImmutable()));
        $this->assertFalse($sut->isValid((string) $token));
    }

    #[TestDox("Shall know if any of the tokens it contains is expired")]
    public function test2a()
    {
        $sut = new MemoryTokenStorageStub();
        $invalidTokens = array_map(static fn () => new CsrfToken(new DateTimeImmutable("yesterday")), range(1, 3));
        $token = new CsrfToken(new DateTimeImmutable());
        array_walk($invalidTokens, $sut->add(...));
        $sut->add($token);
        $this->assertTrue($sut->isValid((string) $token));
    }

    #[TestDox("Shall know if any of the tokens it contains matches")]
    public function test2z()
    {
        $sut = new MemoryTokenStorageStub();
        $invalidTokens = array_map(static fn () => new CsrfToken(new DateTimeImmutable("now")), range(1, 3));
        $token = new CsrfToken(new DateTimeImmutable());
        array_walk($invalidTokens, $sut->add(...));
        $sut->add($token);
        $this->assertTrue($sut->isValid((string) $token));
    }

    #[TestDox("Shall say a token is invalid if it is not contained in the storage")]
    public function test3()
    {
        $sut = new MemoryTokenStorageStub();
        $token = new CsrfToken(new DateTimeImmutable());
        $this->assertFalse($sut->isValid((string) $token));
    }

    #[TestDox("Shall clear all expired tokens")]
    public function test4()
    {
        $sut = new MemoryTokenStorageStub();
        $expiredToken = new CsrfToken(new DateTimeImmutable("last month"));
        $freshToken = new CsrfToken(new DateTimeImmutable("now"));
        $sut->add($expiredToken);
        $sut->add($freshToken);
        $tokens = $sut->queryAll();
        foreach ($tokens as $token) {
            $this->assertFalse($token->isExpired(), "The storage did not clear all expired tokens");
        }
    }

    #[TestDox("Shall not allow more than " . TOKEN_MAX . " tokens")]
    #[DataProvider("excessiveTokenCount")]
    public function test5(array $excessiveTokens)
    {
        $sut = new MemoryTokenStorageStub();
        foreach ($excessiveTokens as $token) {
            $sut->add($token);
        }
        $tokens = $sut->queryAll();
        $this->assertCount(TOKEN_MAX, $tokens);
    }

    #[TestDox("Shall say token is invalid when it does not contain any tokens")]
    public function test6()
    {
        $sut = new MemoryTokenStorageStub();
        $token = new CsrfToken(new DateTimeImmutable());
        $this->assertFalse($sut->isValid((string) $token));
    }
}
