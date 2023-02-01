<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddlewareFactory
 * @uses \Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddleware
 */
final class CsrfPostRoutingMiddlewareFactoryTest extends TestCase
{
    /**
     * @test
     * @testdox Shall create a CsrfPostRoutingMiddleware
     */
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $tokenStorage = new MemoryTokenStorageStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $routingResponseBody = $streamFactory->createStream();
        $routingResponse = $responseFactory->createResponse()->withBody($routingResponseBody);
        $sut = new CsrfPostRoutingMiddlewareFactory($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $result = $sut->getMiddleware($routingResponse);
        $this->assertInstanceOf(CsrfPostRoutingMiddleware::class, $result);
    }
}
