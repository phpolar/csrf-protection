<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfResponseFilter\Http\Message\CsrfResponseFilter;
use Phpolar\CsrfResponseFilter\Http\Message\ResponseFilterPatternStrategy;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

#[CoversClass(CsrfResponseFilterMiddleware::class)]
#[UsesClass(CsrfProtectionRequestHandler::class)]
#[UsesClass(AbstractTokenStorage::class)]
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

        $streamFactory = new StreamFactoryStub("w+");
        $responseFactory = new ResponseFactoryStub($streamFactory->createStream($template));
        $tokenStorage = new MemoryTokenStorageStub();

        $validToken = new CsrfToken(new DateTimeImmutable("now"));
        $request = (new RequestStub("GET"))->withQueryParams([REQUEST_ID_KEY => (string) $validToken]);
        $sut = new CsrfResponseFilterMiddleware(
            new CsrfResponseFilter(
                new ResponseFilterPatternStrategy(
                    $validToken,
                    $streamFactory,
                    $this->tokenKey,
                ),
            ),
        );
        $routingHandler = new CsrfProtectionRequestHandler($validToken, $tokenStorage, $responseFactory);
        $responseWithFormKeys = $sut->process($request, $routingHandler);
        $token = $validToken;
        $tokenForUri = urlencode((string) $token);
        $expected = <<<HTML
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <form action="somewhere" method="post">
            <input type="hidden" name="{$this->tokenKey}" value="{$validToken}" />
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
