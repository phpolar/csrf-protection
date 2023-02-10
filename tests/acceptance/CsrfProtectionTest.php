<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class CsrfProtectionTest extends TestCase
{
    /**
     * @test
     * @testdox Shall prevent a CSRF attack when token does not exist in $requestType request
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::tokenNotExists()
     */
    public function criterion1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $handler->handle($request);
        $this->assertSame(
            400,
            $response->getStatusCode()
        );
    }

    /**
     * @test
     * @testdox Shall prevent a CSRF attack when token is expired in $requestType request
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::tokenExpired()
     */
    public function criterion2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $handler->handle($request);
        $this->assertSame(
            403,
            $response->getStatusCode()
        );
    }

    /**
     * @test
     * @testdox Shall allow request when token is valid in $requestType request
     * @dataProvider \Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider::validToken()
     */
    public function criterion3(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        int $expectedCode,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfCheckRequestHandler($responseFactory, $tokenStorage);
        $response = $handler->handle($request);
        $this->assertSame(
            $expectedCode,
            $response->getStatusCode()
        );
    }
}
