<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use PhpCommonEnums\HttpResponseCode\Enumeration\HttpResponseCodeEnum as ResponseCode;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(CsrfProtectionRequestHandler::class)]
#[UsesClass(CsrfToken::class)]
#[UsesClass(AbstractTokenStorage::class)]
final class CsrfProtectionRequestHandlerTest extends TestCase
{
    #[TestDox("Shall return 'Created' HTTP Response when token is valid and request method is POST")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithPostRequest")]
    public function testPost1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Created->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is GET")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithGetRequest")]
    public function testGet1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when query params do not exist and request method is GET")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "noTokenWithNoQueryParamsGetRequest")]
    public function testGet2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is DELETE")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithDeleteRequest")]
    public function testDelete(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is PUT")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithPutRequest")]
    public function testPut(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Method Not Allowed' HTTP Response when token is valid and method is not implemented")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithOtherMethodsRequest")]
    public function testOtherMethods(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::MethodNotAllowed->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is HEAD")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithHeadRequest")]
    public function testHead1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is invalid and request method is HEAD")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidTokenWithHeadRequest")]
    public function testHead2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is OPTIONS")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithHeadRequest")]
    public function testOptions1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is invalid and request method is OPTIONS")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidTokenWithHeadRequest")]
    public function testOptions2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Ok->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Bad Request' HTTP Response when there is no request ID in a \$requestMethod request")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "tokenNotExists")]
    public function testNoId(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestMethod, // used for test dox output
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::BadRequest->getLabel();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Forbidden' HTTP Response when the request ID is invalid in a \$requestMethod request")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidToken")]
    public function testInvalid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestMethod, // used for test dox output
    ) {
        $sut = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = ResponseCode::Forbidden->getLabel();
        $this->assertSame($expected, $actual);
    }
}
