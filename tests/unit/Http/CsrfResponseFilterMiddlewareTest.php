<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\CsrfTokenGenerator;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfResponseFilter\Http\Message\ResponseFilterPatternStrategy;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

#[CoversClass(CsrfResponseFilterMiddleware::class)]
#[UsesClass(CsrfProtectionRequestHandler::class)]
#[UsesClass(AbstractTokenStorage::class)]
#[UsesClass(ResponseFilterPatternStrategy::class)]
#[UsesClass(CsrfToken::class)]
final class CsrfResponseFilterMiddlewareTest extends TestCase
{
    private $tokenKey = REQUEST_ID_KEY;

    private StreamInterface $stream;

    public function tearDown(): void
    {
        if (isset($this->stream) === true) {
            $this->stream->close();
        }
    }

    #[Test]
    #[TestDox("Shall attach a request id to all links and forms and add it to storage")]
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

        /**
         * @var Stub&RequestHandlerInterface $routingHandlerStub
         */
        $routingHandlerStub = $this->createStub(RequestHandlerInterface::class);
        /**
         * @var Stub&CsrfTokenGenerator $tokenGenerator
         */
        $tokenGenerator = $this->createStub(CsrfTokenGenerator::class);
        $responseFactory = new ResponseFactoryStub();
        $tokenStorage = new MemoryTokenStorageStub();
        $streamFactory = new StreamFactoryStub("w+");

        $validToken = new CsrfToken(new DateTimeImmutable("now"));
        $request = (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]);
        $tokenGenerator->method("generate")->willReturn($validToken);
        $routingResponse = $responseFactory
            ->createResponse()
            ->withBody(
                $streamFactory->createStream($template)
            );
        $routingHandlerStub->method("handle")->willReturn($routingResponse);
        $sut = new CsrfResponseFilterMiddleware(
            $tokenStorage,
            $tokenGenerator,
            new ResponseFilterPatternStrategy(
                $validToken,
                $streamFactory,
                $this->tokenKey,
            ),
        );
        $responseWithFormKeys = $sut->process($request, $routingHandlerStub);
        $token = $tokenStorage->queryOne(0);
        $tokenForUri = urlencode((string) $token);
        $expected = <<<HTML
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form action="somewhere" method="post">
            <input type="hidden" name="{$this->tokenKey}" value="{$token}" />
        </form>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token}" />
        </form>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token}" />
        </form>
        HTML;
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }
}
