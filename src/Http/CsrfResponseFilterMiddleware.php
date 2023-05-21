<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\Http\Message\ResponseFilterInterface;
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
        private CsrfToken $token,
        private AbstractTokenStorage $storage,
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
        $this->storage->add($this->token);
        $response = $handler->handle($request);
        return $this->responseFilter->filter($response);
    }
}
