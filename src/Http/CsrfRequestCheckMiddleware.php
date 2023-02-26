<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\HttpCodes\ResponseCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Takes care of request validation logic for CSRF attack mitigation
 */
class CsrfRequestCheckMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestHandlerInterface $csrfCheckHandler)
    {
    }

    /**
     * Produces a response for an invalid request or
     * delegates request handling to the provided handler.
     *
     * if the request fails the check,
     * this middleware will return a *canned response*
     * with a response that is either **Method Not Allowed**,
     * **Bad Request** or **Forbidden**.
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
