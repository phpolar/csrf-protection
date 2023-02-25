<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

#[CoversClass(ResponseFilterContext::class)]
#[CoversClass(ResponseFilterPatternStrategy::class)]
#[CoversClass(ResponseFilterScanStrategy::class)]
#[UsesClass(CsrfToken::class)]
final class ResponseFilterContextTest extends TestCase
{
    private string $tokenKey = REQUEST_ID_KEY;

    private StreamInterface $stream;

    public function tearDown(): void
    {
        if (isset($this->stream) === true) {
            $this->stream->close();
        }
    }

    #[Test]
    #[TestDox("Shall attach a request id to each form when a session is active")]
    public function forms()
    {
        $token = new CsrfToken(new DateTimeImmutable("now"));
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $sut = new ResponseFilterContext(new ResponseFilterPatternStrategy($token, $streamFactory));
        $forms = <<<HTML
        <form action="somewhere" method="post"></form>
        <form></form>
        <form></form>
        HTML;
        $expected = <<<HTML
        <form action="somewhere" method="post">
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        <form>
            <input type="hidden" name="{$this->tokenKey}" value="{$token->asString()}" />
        </form>
        HTML;
        $this->stream = $streamFactory->createStream($forms);
        $response = $responseFactory->createResponse();
        $responseWithFormKeys = $sut->transform($response->withBody($this->stream));
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }

    #[TestDox("Shall not attach a request id when there are no forms")]
    public function noop()
    {
        $token = new CsrfToken(new DateTimeImmutable("now"));
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $sut = new ResponseFilterContext(new ResponseFilterPatternStrategy($token, $streamFactory));
        $forms = <<<HTML
        <p></p>
        <p></p>
        <p></p>
        HTML;
        $expected = <<<HTML
        <p></p>
        <p></p>
        <p></p>
        HTML;
        $this->stream = $streamFactory->createStream($forms);
        $response = $responseFactory->createResponse();
        $responseWithFormKeys = $sut->transform($response->withBody($this->stream));
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }

    #[Test]
    #[TestDox("Shall attach a request id to all links")]
    public function links()
    {
        $token = new CsrfToken(new DateTimeImmutable("now"));
        $tokenForUri = urlencode($token->asString());
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $sut = new ResponseFilterContext(new ResponseFilterPatternStrategy($token, $streamFactory));
        $links = <<<HTML
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        HTML;
        $expected = <<<HTML
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        <a href="http://somewhere.com?{$this->tokenKey}={$tokenForUri}&action=doSomething">some text</a>
        HTML;
        $this->stream = $streamFactory->createStream($links);
        $response = $responseFactory->createResponse();
        $responseWithFormKeys = $sut->transform($response->withBody($this->stream));
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }

    #[Test]
    #[TestDox("Shall attach a request id to all links and forms")]
    public function linksAndForms()
    {
        $token = new CsrfToken(new DateTimeImmutable("now"));
        $tokenForUri = urlencode($token->asString());
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $sut = new ResponseFilterContext(new ResponseFilterScanStrategy($token, $responseFactory, $streamFactory));
        $links = <<<HTML
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form action="somewhere" method="post"></form>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form></form>
        <a href="http://somewhere.com?action=doSomething">some text</a>
        <form></form>
        HTML;
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
        $this->stream = $streamFactory->createStream($links);
        $response = $responseFactory->createResponse();
        $responseWithFormKeys = $sut->transform($response->withBody($this->stream));
        $actual = $responseWithFormKeys->getBody()->getContents();
        $this->assertSame($expected, $actual);
    }
}
