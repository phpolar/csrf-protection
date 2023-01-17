<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Phpolar\CsrfProtection\Http\CsrfCheckRequestHandler
 * @uses Phpolar\CsrfProtection\CsrfToken
 * @uses Phpolar\CsrfProtection\Storage\AbstractTokenStorage
 */
final class CsrfCheckRequestHandlerTest extends TestCase
{
    /**
     * @testdox Shall return 'Created' HTTP Response when token is valid and request method is POST
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithPostRequest()
     */
    public function testPost1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::CREATED;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is valid and request method is GET
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithGetRequest()
     */
    public function testGet1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when query params do not exist and request method is GET
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::noTokenWithNoQueryParamsGetRequest()
     */
    public function testGet2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is valid and request method is DELETE
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithDeleteRequest()
     */
    public function testDelete(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is valid and request method is PUT
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithPutRequest()
     */
    public function testPut(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'Method Not Allowed' HTTP Response when token is valid and method is not implemented
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithOtherMethodsRequest()
     */
    public function testOtherMethods(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::METHOD_NOT_ALLOWED;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is valid and request method is HEAD
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithHeadRequest()
     */
    public function testHead1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is invalid and request method is HEAD
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::invalidTokenWithHeadRequest()
     */
    public function testHead2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is valid and request method is OPTIONS
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validTokenWithHeadRequest()
     */
    public function testOptions1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'OK' HTTP Response when token is invalid and request method is OPTIONS
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::invalidTokenWithHeadRequest()
     */
    public function testOptions2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::OK;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'Bad Request' HTTP Response when there is no request ID in the request data
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::tokenNotExists()
     */
    public function testNoId(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::BAD_REQUEST;
        $this->assertSame($expected, $actual);
    }

    /**
     * @testdox Shall return 'Forbidden' HTTP Response when the token is invalid
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::invalidToken()
     */
    public function testInvalid(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::FORBIDDEN;
        $this->assertSame($expected, $actual);
    }

    /**
     * Shall log forbidden requests when a logger is provided
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::invalidToken()
     */
    public function testLogging(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
    ) {
        /**
         * @var MockObject|LoggerInterface
         */
        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock->expects($this->atLeastOnce())->method("warning");
        $sut = new CsrfCheckRequestHandler($responseFactory, $tokenStorage, $loggerMock);
        $response = $sut->handle($request);
        $actual = $response->getReasonPhrase();
        $expected = CsrfCheckRequestHandler::FORBIDDEN;
        $this->assertSame($expected, $actual);
    }
}
;