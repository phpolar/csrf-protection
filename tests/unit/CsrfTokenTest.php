<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(CsrfToken::class)]
final class CsrfTokenTest extends TestCase
{
    #[TestDox("Shall be convertible to a string")]
    public function test1()
    {
        $token = new CsrfToken(
            new DateTimeImmutable("now")
        );
        $this->assertIsString((string) $token);
    }

    #[TestDox("Shall know if it represents a string")]
    public function test2()
    {
        $token = new CsrfToken(
            new DateTimeImmutable("now")
        );
        $tokenAsString = (string) $token;
        $this->assertTrue($token->represents($tokenAsString));
    }

    #[TestDox("Shall know if it is expired")]
    public function test3()
    {
        $createdOn = new DateTimeImmutable("now");
        $secondsToLive = 9;
        $token = new CsrfToken(
            $createdOn->sub(new DateInterval("PT10S")),
            $secondsToLive
        );
        $this->assertTrue($token->isExpired());
    }

    #[TestDox("Shall know if it is not expired")]
    public function test4()
    {
        $createdOn = new DateTimeImmutable("now");
        $token = new CsrfToken($createdOn);
        $this->assertFalse($token->isExpired());
    }

    #[TestDox("Shall handle negative ttl values")]
    public function test5()
    {
        $secondsToLive = -1;
        $token = new CsrfToken(
            new DateTimeImmutable("now"),
            $secondsToLive,
        );
        $this->assertTrue($token->isExpired());
    }
}
