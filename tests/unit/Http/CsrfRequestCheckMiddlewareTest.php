<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseStub;
use Phpolar\HttpCodes\ResponseCode;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @covers \Phpolar\CsrfProtection\Http\CsrfRequestCheckMiddleware
 * @covers \Phpolar\CsrfProtection\Http\AbstractCsrfProtectionMiddleware
 * @uses \Phpolar\CsrfProtection\Http\CsrfProtectionRequestHandler
 * @uses \Phpolar\CsrfProtection\Storage\AbstractTokenStorage
 * @uses \Phpolar\CsrfProtection\Http\ResponseFilterContext
 * @uses \Phpolar\CsrfProtection\Http\ResponseFilterScanStrategy
 * @uses \Phpolar\CsrfProtection\CsrfToken
 */
final class CsrfPreRoutingMiddlewareTest extends TestCase
{
    private StreamInterface $stream;

    public function tearDown(): void
    {
        if (isset($this->stream) === true) {
            $this->stream->close();
        }
    }

    /**
     * @test
     * @testdox Shall determine if a request is forbidden
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::invalidToken()
     *
     */
    public function tokenInvalid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $routingResponse = new ResponseStub();
        $sut = new CsrfRequestCheckMiddleware($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->process($request, $handler);
        $this->assertSame(ResponseCode::FORBIDDEN, $response->getStatusCode());
    }

    /**
     * @test
     * @testdox Shall return continue response if request is valid
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithPostRequest()
     *
     */
    public function tokenValid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $routingResponse = new ResponseStub();
        $sut = new CsrfRequestCheckMiddleware($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->process($request, $handler);
        $this->assertSame(ResponseCode::CONTINUE, $response->getStatusCode());
    }
}
