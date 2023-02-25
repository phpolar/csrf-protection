<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CsrfTokenGenerator::class)]
#[UsesClass(CsrfToken::class)]
final class CsrfTokenGeneratorTest extends TestCase
{
    #[TestDox("Shall produce a valid token")]
    public function test1()
    {
        $sut = new CsrfTokenGenerator();
        $token = $sut->generate();
        $this->assertFalse($token->isExpired());
    }

    #[TestDox("Shall allow time-to-live to be configurable")]
    public function test2()
    {
        $givenTtl = -1;
        $sut = new CsrfTokenGenerator($givenTtl);
        $token = $sut->generate();
        $this->assertTrue($token->isExpired(), "Time-to-live is not configurable.");
    }
}
