<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\HttpCodes\ResponseCode;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Adds support for CSRF attack mitigation
 */
class CsrfRequestCheckMiddleware extends AbstractCsrfProtectionMiddleware
{
    /**
     * Provide protection against CSRF attack.
     *
     * If the request fails the check,
     * the provided request handler will be used
     * to create the request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $csrfHandler = $this->getHandler();
        $response = $csrfHandler->handle($request);
        if ($response->getStatusCode() === ResponseCode::FORBIDDEN) {
            return $handler->handle($request);
        }
        return $response->withStatus(ResponseCode::CONTINUE);
    }
}
