<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Exception;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class MemoryROStreamFactoryStub implements StreamFactoryInterface
{
    public function createStream(string $content = ''): StreamInterface
    {
        return new MemoryStreamStub($content, "r");
    }

    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        throw new Exception("Not Implemented");
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        throw new Exception("Not Implemented");
    }
}
