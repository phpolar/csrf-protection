<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractSession::class)]
final class AbstractSessionTest extends TestCase
{
    public static function scenarios(): \Generator
    {
        $givenKey = uniqid();
        $givenValue = uniqid();
        $values = [];
        $sut = new class ($values) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return false;
            }
        };
        yield [
            $givenKey,
            $givenValue,
            $sut,
        ];
    }

    #[TestDox("Shall set an entry and get the value")]
    #[DataProvider("scenarios")]
    public function testa(string $givenKey, string $givenValue, AbstractSession $sut)
    {
        $sut[$givenKey] = $givenValue;
        $this->assertSame($givenValue, $sut[$givenKey]);
    }

    #[TestDox("Shall know if a given key is set")]
    #[DataProvider("scenarios")]
    public function testb(string $givenKey, string $givenValue, AbstractSession $sut)
    {
        $sut[$givenKey] = $givenValue;
        $this->assertTrue(isset($sut[$givenKey]));
    }

    #[TestDox("Shall allow for unsetting entries")]
    #[DataProvider("scenarios")]
    public function testc(string $givenKey, string $givenValue, AbstractSession $sut)
    {
        $sut[$givenKey] = $givenValue;
        unset($sut[$givenKey]);
        $this->assertFalse(isset($sut[$givenKey]));
    }

    #[TestDox("Shall know if a given entry is not set")]
    #[DataProvider("scenarios")]
    public function testd(string $givenKey, string $givenValue, AbstractSession $sut)
    {
        $this->assertFalse(isset($sut[$givenKey]));
    }

    #[TestDox("Shall know if a session is not active")]
    public function teste()
    {
        $fakeSession = [];
        $sut = new class ($fakeSession) extends AbstractSession
        {
            public function isNotActive(): bool
            {
                return true;
            }
        };
        $this->assertTrue($sut->isNotActive());
    }
}
