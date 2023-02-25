<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryStreamStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseStub;
use Phpolar\HttpCodes\ResponseCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $handlerStub = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new ResponseStub();
            }
        };
        $sut = new CsrfRequestCheckMiddleware($handler);
        $response = $sut->process($request, $handlerStub);
        $this->assertSame(ResponseCode::FORBIDDEN, $response->getStatusCode());
    }

    #[Test]
    #[TestDox("Shall return the response from the provided \"next\" handler if the request is valid")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithPostRequest")]
    public function tokenValid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $expectedResponseContent = "The request is safe!";
        $handler = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $handlerStub = new class ($expectedResponseContent) implements RequestHandlerInterface {
            public function __construct(private string $expectedResponseContent)
            {
            }
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseStub(ResponseCode::OK))->withBody(new MemoryStreamStub($this->expectedResponseContent));
            }
        };
        $sut = new CsrfRequestCheckMiddleware($handler);
        $response = $sut->process($request, $handlerStub);
        $this->assertSame(ResponseCode::OK, $response->getStatusCode());
        $this->assertSame($expectedResponseContent, $response->getBody()->getContents());
    }
}
