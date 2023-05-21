<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\DataProviders\CsrfCheckDataProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;

#[RunTestsInSeparateProcesses]
#[CoversNothing]
final class CsrfProtectionTest extends TestCase
{
    #[Test]
    #[TestDox("Shall prevent a CSRF attack when token does not exist in \$requestType request")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "tokenNotExists")]
    public function criterion1(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $handler->handle($request);
        $this->assertSame(
            400,
            $response->getStatusCode()
        );
    }

    #[Test]
    #[TestDox("Shall prevent a CSRF attack when token is expired in \$requestType request")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "tokenExpired")]
    public function criterion2(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $handler->handle($request);
        $this->assertSame(
            403,
            $response->getStatusCode()
        );
    }

    #[Test]
    #[TestDox("Shall allow request when token is valid in \$requestType request")]
    #[DataProviderExternal(CsrfCheckDataProvider::class, "validToken")]
    public function criterion3(
        ServerRequestInterface $request,
        AbstractTokenStorage $tokenStorage,
        ResponseFactoryInterface $responseFactory,
        int $expectedCode,
        string $requestType // variable used in testdox input
    ) {
        $handler = new CsrfProtectionRequestHandler(
            new CsrfToken(new DateTimeImmutable("now")),
            $tokenStorage,
            $responseFactory,
        );
        $response = $handler->handle($request);
        $this->assertSame(
            $expectedCode,
            $response->getStatusCode()
        );
    }
}
