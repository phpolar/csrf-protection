<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use DateInterval;
use DateTimeImmutable;
use Generator;
use Phpolar\CsrfProtection\CsrfToken;
use PHPUnit\Framework\TestCase;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
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

    public static function invalidTokens(): Generator
    {
        yield [new CsrfToken((new DateTimeImmutable())->sub(new DateInterval("PT10S")), 9)];
        yield [new CsrfToken(new DateTimeImmutable("2000-10-10"))];
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
        $this->assertTrue($sut->isValid($token->asString()));
    }

    #[TestDox("Shall know if a token is invalid")]
    #[DataProvider("invalidTokens")]
    public function test2(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertFalse($sut->isValid($token->asString()));
    }

    #[TestDox("Shall say a token is invalid if it is not contained in the storage")]
    #[DataProvider("invalidTokens")]
    public function test3(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertFalse($sut->isValid($token->asString()));
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
    #[Group("me")]
    public function test5(array $excessiveTokens)
    {
        $sut = new MemoryTokenStorageStub();
        foreach ($excessiveTokens as $token) {
            $sut->add($token);
        }
        $tokens = $sut->queryAll();
        $this->assertCount(TOKEN_MAX, $tokens);
    }
}
