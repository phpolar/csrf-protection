<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\HttpCodes\ResponseCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Adds support for CSRF attack mitigation
 */
class CsrfRequestCheckMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestHandlerInterface $csrfCheckHandler)
    {
    }

    /**
     * Provide protection against CSRF attack.
     *
     * If the request fails the check,
     * the provided request handler will be used
     * to create the request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->csrfCheckHandler->handle($request);
        if ($response->getStatusCode() === ResponseCode::FORBIDDEN) {
            return $response;
        }
        return $handler->handle($request);
    }
}
