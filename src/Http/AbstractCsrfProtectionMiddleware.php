<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use DateTimeImmutable;
use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;
use Phpolar\CsrfProtection\Storage\SessionTokenStorage;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Adds support for CSRF attack mitigation
 */
abstract class AbstractCsrfProtectionMiddleware implements MiddlewareInterface
{
    private string $requestId = REQUEST_ID_KEY;

    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
        protected StreamFactoryInterface $streamFactory,
        protected ?AbstractTokenStorage $storage = null,
        protected ?LoggerInterface $logger = null,
        protected ?ResponseFilterStrategyInterface $filterStrategy = null,
    ) {
    }

    /**
     * Configure logging
     * @codeCoverageIgnore
     */
    public function useLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * Allows configuring a provided storage mechanism for the CSRF token
     * @codeCoverageIgnore
     */
    public function useStorage(AbstractTokenStorage $storage): self
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * Allows configuring a provided response filter strategy
     * @codeCoverageIgnore
     */
    public function useResponseFilterStrategy(ResponseFilterStrategyInterface $filterStrategy): self
    {
        $this->filterStrategy = $filterStrategy;
        return $this;
    }

    /**
     * Provide protection against CSRF attack.
     *
     * If the request fails the check,
     * the provided request handler will be used
     * to create the request.
     */
    abstract public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;

    protected function getHandler(): CsrfProtectionRequestHandler
    {
        return new CsrfProtectionRequestHandler(
            $this->responseFactory,
            $this->getTokenStorage(),
            $this->logger,
            $this->requestId,
        );
    }

    protected function getResponseFilter(CsrfToken $token): ResponseFilterContext
    {
        return new ResponseFilterContext(
            $this->filterStrategy ?? new ResponseFilterScanStrategy(
                $token,
                $this->responseFactory,
                $this->streamFactory,
                $this->requestId
            )
        );
    }

    protected function getToken(): CsrfToken
    {
        return new CsrfToken(new DateTimeImmutable("now"));
    }

    protected function getTokenStorage(): AbstractTokenStorage
    {
        return $this->storage = ($this->storage ?? new SessionTokenStorage());
    }
}
