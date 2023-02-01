<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfProtection\Tests\Stubs\RequestStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * @covers \Phpolar\CsrfProtection\Http\CsrfPostRoutingMiddleware
 * @covers \Phpolar\CsrfProtection\Http\AbstractCsrfProtectionMiddleware
 * @uses \Phpolar\CsrfProtection\Http\CsrfCheckRequestHandler
 * @uses \Phpolar\CsrfProtection\Storage\AbstractTokenStorage
 * @uses \Phpolar\CsrfProtection\Http\ResponseFilterContext
 * @uses \Phpolar\CsrfProtection\Http\ResponseFilterScanStrategy
 * @uses \Phpolar\CsrfProtection\CsrfToken
 */
final class CsrfPostRoutingMiddlewareTest extends TestCase
{
    private $tokenKey = REQUEST_ID_KEY;

    private StreamInterface $stream;

    public function tearDown(): void
    {
        if (isset($this->stream) === true) {
            $this->stream->close();
        }
    }

    /**
     * @test
     * @testdox Shall attach a request id to all links and forms
     */
    public function linksAndForms()
    {
        $template = <<<HTML
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form action="somewhere" method="post"></form>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form></form>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form></form>
        HTML;

        $requestHandlerStub = $this->createStub(RequestHandlerInterface::class);
        $responseFactory = new ResponseFactoryStub();
        $tokenStorage = new MemoryTokenStorageStub();
        $streamFactory = new MemoryRWStreamFactoryStub();

        $validToken = new CsrfToken(new DateTimeImmutable("now"));
        $tokenStorage->add($validToken);
        $request = (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => $validToken->asString()]);

        $routingResponseBody = $streamFactory->createStream($template);
        $routingResponse = $responseFactory->createResponse()->withBody($routingResponseBody);
        $sut = new CsrfPostRoutingMiddleware($routingResponse, $responseFactory, new MemoryRWStreamFactoryStub(), $tokenStorage);
        $responseWithFormKeys = $sut->process($request, $requestHandlerStub);
        $token = $tokenStorage->queryOne(1);
        $tokenForUri = urlencode($token->asString());
        $expected = <<<HTML
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form action="somewhere" method="post">
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        HTML;
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }

}