<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use DateTimeImmutable;
use Phpolar\CsrfProtection\Http\CsrfProtectionRequestHandler;
use Phpolar\CsrfProtection\Http\ResponseFilterContext;
use Phpolar\CsrfProtection\Http\ResponseFilterScanStrategy;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryROStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfProtection\Tests\Stubs\RequestStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[RunTestsInSeparateProcesses]
#[CoversNothing]
final class MemoryUsageTest extends TestCase
{
    private ResponseInterface $response;

    private ServerRequestInterface $request;

    private AbstractTokenStorage $storage;

    private CsrfToken $token;

    public function setUp(): void
    {
        $this->createTokenAndAddToStorage()
            ->createResponse()
            ->createRequest();
    }

    #[Test]
    #[TestDox("Memory usage shall be below " . PROJECT_MEMORY_USAGE_THRESHOLD . " bytes")]
    public function shallBeBelowThreshold()
    {
        $memoryUsed = -memory_get_usage();
        $this->filterResponse()
            ->checkRequest();
        $after = memory_get_usage();
        $memoryUsed += $after;
        $this->assertGreaterThan(0, $memoryUsed);
        $this->assertLessThanOrEqual((int) PROJECT_MEMORY_USAGE_THRESHOLD, $memoryUsed);
    }

    private function createTokenAndAddToStorage(): self
    {
        $this->token = new CsrfToken(new DateTimeImmutable());
        $this->storage = new MemoryTokenStorageStub();
        $this->storage->add($this->token);
        return $this;
    }

    private function createResponse(): self
    {
        $responseFactory = new ResponseFactoryStub();
        $streamFactory = new MemoryROStreamFactoryStub();
        $response = $responseFactory->createResponse();
        $stream = $streamFactory->createStream(
            <<<HTML
            <form action="somewhere" method="post">
                <input type="text" name="something" />
                <input type="submit" value="Submit" />
            </form>
            HTML
        );
        $this->response = $response->withBody($stream);
        return $this;
    }

    private function createRequest(): self
    {
        $request = new RequestStub();
        $this->request = $request->withParsedBody((object) [REQUEST_ID_KEY => $this->token]);
        return $this;
    }

    private function filterResponse(): self
    {
        $responseFilter = new ResponseFilterContext(
            new ResponseFilterScanStrategy(
                $this->token,
                new ResponseFactoryStub(),
                new MemoryRWStreamFactoryStub()
            )
        );
        $responseFilter->transform($this->response);
        return $this;
    }

    private function checkRequest(): self
    {
        $handler = new CsrfProtectionRequestHandler(
            new ResponseFactoryStub(),
            new MemoryTokenStorageStub(),
        );
        $handler->handle($this->request);
        return $this;
    }
}
