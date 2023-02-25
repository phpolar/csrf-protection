<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

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
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::CREATED;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is GET")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithGetRequest")]
    public function testGet1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when query params do not exist and request method is GET")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "noTokenWithNoQueryParamsGetRequest")]
    public function testGet2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is DELETE")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithDeleteRequest")]
    public function testDelete(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is PUT")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithPutRequest")]
    public function testPut(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Method Not Allowed' HTTP Response when token is valid and method is not implemented")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithOtherMethodsRequest")]
    public function testOtherMethods(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::METHOD_NOT_ALLOWED;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is HEAD")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithHeadRequest")]
    public function testHead1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is invalid and request method is HEAD")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidTokenWithHeadRequest")]
    public function testHead2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is valid and request method is OPTIONS")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validTokenWithHeadRequest")]
    public function testOptions1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'OK' HTTP Response when token is invalid and request method is OPTIONS")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidTokenWithHeadRequest")]
    public function testOptions2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Bad Request' HTTP Response when there is no request ID in the request data")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "tokenNotExists")]
    public function testNoId(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::BAD_REQUEST;
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall return 'Forbidden' HTTP Response when the token is invalid")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "invalidToken")]
    public function testInvalid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfProtectionRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfProtectionRequestHandler::FORBIDDEN;
        $this->assertSame($expected, $actual);
    }
}
