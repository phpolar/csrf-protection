<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\CsrfTokenGenerator;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Adds support for CSRF attack mitigation
 * by attaching identifiers to the valid
 * response.
 */
class CsrfResponseFilterMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AbstractTokenStorage $storage,
        private CsrfTokenGenerator $tokenGenerator,
        private ResponseFilterStrategyInterface $filterStrategy,
        private ?LoggerInterface $logger = null,
    ) {
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
        $token = $this->tokenGenerator->generate();
        $this->storage->add($token);
        $response = $handler->handle($request);
        return $this->filterStrategy->algorithm($response);
    }
}
