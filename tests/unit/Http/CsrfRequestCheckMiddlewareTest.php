<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as ResponseCode;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider;
use Phpolar\HttpMessageTestUtils\MemoryStreamStub;
use Phpolar\HttpMessageTestUtils\ResponseStub;
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
#[UsesClass(CsrfProtectionRequestHandler::class)]
#[UsesClass(AbstractTokenStorage::class)]
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
        string $method, // now required since phpunit 12.4
    ) {
        $handler = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $handlerStub = new class() implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new ResponseStub();
            }
        };
        $sut = new CsrfRequestCheckMiddleware($handler);
        $response = $sut->process($request, $handlerStub);
        $this->assertSame(ResponseCode::Forbidden->value, $response->getStatusCode());
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
        $handler = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $handlerStub = new class($expectedResponseContent) implements RequestHandlerInterface {
            public function __construct(private string $expectedResponseContent) {}
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return (new ResponseStub(ResponseCode::Ok->value))->withBody(new MemoryStreamStub($this->expectedResponseContent));
            }
        };
        $sut = new CsrfRequestCheckMiddleware($handler);
        $response = $sut->process($request, $handlerStub);
        $this->assertSame(ResponseCode::Ok->value, $response->getStatusCode());
        $this->assertSame($expectedResponseContent, $response->getBody()->getContents());
    }
}
