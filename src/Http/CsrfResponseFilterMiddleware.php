<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use PhpContrib\Http\Message\ResponseFilterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Adds support for CSRF attack mitigation
 * by attaching identifiers to the valid
 * response.
 */
class CsrfResponseFilterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseFilterInterface $responseFilter,
    ) {
    }

    /**
     * Stores a *request validation token* in
     * server state and attaches the token to the
     * response.
     *
     * The stored token SHOULD then be used to validate
     * futher requests.
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);
        return $this->responseFilter->filter($response);
    }
}
