<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Use to create the CsrfPostRoutingMiddleware.
 *
 * Creating the CsrfPostRoutingMiddleware requires
 * the routing response which will be created
 * after a dependency injection is configured.
 */
final class CsrfPostRoutingMiddlewareFactory
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected ?AbstractTokenStorage $storage = null,
        protected ?LoggerInterface $logger = null,
        protected ?ResponseFilterStrategyInterface $filterStrategy = null,
    ) {
    }

    /**
     * Gets an instance of CsrfPostRoutingMiddleware
     */
    public function getMiddleware(ResponseInterface $response): CsrfPostRoutingMiddleware
    {
        return new CsrfPostRoutingMiddleware(
            $response,
            $this->responseFactory,
            $this->streamFactory,
            $this->storage,
            $this->logger,
            $this->filterStrategy,
        );
    }
}
