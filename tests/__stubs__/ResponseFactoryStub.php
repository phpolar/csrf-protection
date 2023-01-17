<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

final class ResponseFactoryStub implements ResponseFactoryInterface
{
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new ResponseStub($code, $reasonPhrase);
    }
}
