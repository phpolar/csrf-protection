<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use DateInterval;
use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use PHPUnit\Framework\TestCase;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;

/**
 * @covers \Phpolar\CsrfProtection\Storage\AbstractTokenStorage
 * @uses \Phpolar\CsrfProtection\CsrfToken
 */
final class AbstractTokenStorageTest extends TestCase
{
    public function validTokens(): array
    {
        return [
            [new CsrfToken(new DateTimeImmutable())],
            [new CsrfToken(new DateTimeImmutable())],
            [new CsrfToken(new DateTimeImmutable())],
            [new CsrfToken(new DateTimeImmutable())],
            [new CsrfToken(new DateTimeImmutable())],
        ];
    }

    public function invalidTokens(): array
    {
        return [
            [new CsrfToken((new DateTimeImmutable())->sub(new DateInterval("PT10S")), 9)],
            [new CsrfToken(new DateTimeImmutable("2000-10-10"))],
        ];
    }

    /**
     * @testdox Shall know if a token is valid
     * @dataProvider validTokens()
     */
    public function test1(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertTrue($sut->isValid($token->asString()));
    }

    /**
     * @testdox Shall know if a token is invalid
     * @dataProvider invalidTokens()
     */
    public function test2(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertFalse($sut->isValid($token->asString()));
    }

    /**
     * @testdox Shall say a token is invalid if it is not contained in the storage
     * @dataProvider invalidTokens()
     */
    public function test3(CsrfToken $token)
    {
        $sut = new MemoryTokenStorageStub();
        $sut->add($token);
        $this->assertFalse($sut->isValid($token->asString()));
    }

    /**
     * @testdox Shall clear all expired tokens
     */
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
}
