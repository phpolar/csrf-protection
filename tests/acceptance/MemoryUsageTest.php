<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use DateTimeImmutable;
use Generator;
use Phpolar\CsrfProtection\Http\CsrfProtectionRequestHandler;
use Phpolar\CsrfProtection\Http\CsrfResponseFilterMiddleware;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryTokenStorageStub;
use Phpolar\CsrfResponseFilter\Http\Message\CsrfResponseFilter;
use Phpolar\CsrfResponseFilter\Http\Message\ResponseFilterPatternStrategy;
use Phpolar\HttpMessageTestUtils\RequestStub;
use Phpolar\HttpMessageTestUtils\ResponseFactoryStub;
use Phpolar\HttpMessageTestUtils\StreamFactoryStub;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

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

    public static function thresholds(): Generator
    {
        yield [(int) PROJECT_MEMORY_USAGE_THRESHOLD];
    }

    #[Test]
    #[TestDox("Memory usage shall be below \$threshold bytes")]
    #[DataProvider("thresholds")]
    public function shallBeBelowThreshold(int $threshold)
    {
        $responseFilterMiddleware = new CsrfResponseFilterMiddleware(
            new MemoryTokenStorageStub(),
            new CsrfTokenGenerator(),
            new CsrfResponseFilter(
                new ResponseFilterPatternStrategy(
                    $this->token,
                    new StreamFactoryStub("w+"),
                    REQUEST_ID_KEY,
                ),
            ),
        );
        $handler = new CsrfProtectionRequestHandler(
            new ResponseFactoryStub(),
            new MemoryTokenStorageStub(),
        );
        $request = new RequestStub();
        /**
         * @var MockObject&RequestHandlerInterface $requestHandler
         */
        $requestHandler = $this->createMock(RequestHandlerInterface::class);
        $requestHandler->method("handle")->willReturn($this->response);

        $memoryUsed = -memory_get_usage();
        $handler->handle($this->request);
        $responseFilterMiddleware->process($request, $requestHandler);
        $after = memory_get_usage();
        $memoryUsed += $after;
        $this->assertGreaterThan(0, $memoryUsed);
        $this->assertLessThanOrEqual($threshold, $memoryUsed);
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
        $streamFactory = new StreamFactoryStub("r");
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
}
