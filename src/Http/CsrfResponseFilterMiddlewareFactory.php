<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Use to create the `CsrfResponseFilterMiddleware`.
 *
 * Creating the `CsrfResponseFilterMiddleware` requires
 * the routing response which will be created
 * after dependency injection is configured.
 */
class CsrfResponseFilterMiddlewareFactory
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
     * Gets an instance of CsrfResponseFilterMiddleware
     */
    public function getMiddleware(ResponseInterface $response): CsrfResponseFilterMiddleware
    {
        return new CsrfResponseFilterMiddleware(
            $response,
            $this->responseFactory,
            $this->streamFactory,
            $this->storage,
            $this->logger,
            $this->filterStrategy,
        );
    }
}
