<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

/**
 * Adds support for CSRF attack mitigation
 * by attaching identifiers to the valid
 * response.
 */
class CsrfResponseFilterMiddleware extends AbstractCsrfProtectionMiddleware
{
    public function __construct(
        private ResponseInterface $routingResponse,
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected ?AbstractTokenStorage $storage = null,
        protected ?LoggerInterface $logger = null,
        protected ?ResponseFilterStrategyInterface $filterStrategy = null,
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
        $token = $this->getToken();
        $storage = $this->getTokenStorage();
        $storage->add($token);
        $responseFilter = $this->getResponseFilter($token);
        return $responseFilter->transform($this->routingResponse);
    }
}
