<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryROStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\MemoryRWStreamFactoryStub;
use Phpolar\CsrfProtection\Tests\Stubs\ResponseFactoryStub;

use const Phpolar\CsrfProtection\Tests\CSRF_TEMPLATE_SIZE_FACTOR;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class CsrfResponseFilterBenchmarkTest extends TestCase
{
    /**
     * @testdox Shall efficiently write CSRF protection to the response
     */
    public function test1()
    {
        $responseFactory = new ResponseFactoryStub();
        $streamROFactory = new MemoryROStreamFactoryStub();
        $streamFactory = new MemoryRWStreamFactoryStub();
        $token = new CsrfToken(new DateTimeImmutable("now"));
        $competingAlgo = new ResponseFilterContext(new ResponseFilterPatternStrategy($token, $streamFactory));
        $algoUnderTest = new ResponseFilterContext(new ResponseFilterScanStrategy($token, $responseFactory, $streamFactory));
        $body = str_repeat(
            <<<HTML
            <a href="http://somewhere.com?action=doSomething">some text</a>
            <form></form>
            <div>Hey</div>
            <p></p>
            HTML,
            (int) CSRF_TEMPLATE_SIZE_FACTOR,
        );
        $stream = $streamROFactory->createStream($body);
        $response = $responseFactory->createResponse()->withBody($stream);
        $testAlgoTime = -hrtime(true);
        $resp1 = $algoUnderTest->transform($response);
        $testAlgoTime += hrtime(true);
        $competingAlgoTime = -hrtime(true);
        $resp2 = $competingAlgo->transform($response);
        $competingAlgoTime += hrtime(true);
        $this->assertSame(
            $resp1->getBody()->getContents(),
            $resp2->getBody()->getContents(),
        );
        $stream->close();
        $resp1->getBody()->close();
        $resp2->getBody()->close();
        $this->assertLessThan($competingAlgoTime, $testAlgoTime);
    }
}
