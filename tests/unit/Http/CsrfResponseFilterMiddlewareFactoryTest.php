<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CsrfResponseFilterMiddlewareFactory::class)]
#[UsesClass(CsrfResponseFilterMiddleware::class)]
final class CsrfResponseFilterMiddlewareFactoryTest extends TestCase
{
    #[Test]
    #[TestDox("Shall create a CsrfResponseFilterMiddleware")]
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $tokenStorage = new MemoryTokenStorageStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $routingResponseBody = $streamFactory->createStream();
        $routingResponse = $responseFactory->createResponse()->withBody($routingResponseBody);
        $sut = new CsrfResponseFilterMiddlewareFactory($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $result = $sut->getMiddleware($routingResponse);
        $this->assertInstanceOf(CsrfResponseFilterMiddleware::class, $result);
    }
}
