<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\HttpCodes\ResponseCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

#[CoversClass(CsrfRequestCheckMiddleware::class)]
#[CoversClass(AbstractCsrfProtectionMiddleware::class)]
#[UsesClass(CsrfProtectionRequestHandler::class)]
#[UsesClass(AbstractTokenStorage::class)]
#[UsesClass(CsrfResponseFilter::class)]
#[UsesClass(ResponseFilterScanStrategy::class)]
#[UsesClass(CsrfToken::class)]
final class CsrfRequestCheckMiddlewareTest extends TestCase
{
    private StreamInterface $stream;

    public function tearDown(): void
    {
        if (isset($this->stream) === true) {
            $this->stream->close();
        }
    }

    #[Test]
    #[TestDox("Shall determine if a request is forbidden")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidToken")]
    public function tokenInvalid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfRequestCheckMiddleware($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->process($request, $handler);
        $this->assertSame(ResponseCode::FORBIDDEN, $response->getStatusCode());
    }

    #[Test]
    #[TestDox("Shall return continue response if request is valid")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithPostRequest")]
    public function tokenValid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfRequestCheckMiddleware($responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->process($request, $handler);
        $this->assertSame(ResponseCode::CONTINUE, $response->getStatusCode());
    }
}
