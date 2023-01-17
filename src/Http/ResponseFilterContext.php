<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Provides support for adding CSRF protection
 * to PSR-7 HTTP responses
 */
final class ResponseFilterContext
{
    public function __construct(
        private ResponseFilterStrategyInterface $strategy,
    ) {
    }

    /**
     * Add CSRF protection to the HTTP response
     */
    public function transform(
        ResponseInterface $response,
    ): ResponseInterface {
        return $this->strategy->algorithm($response);
    }
}
