<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * Provides support for swapping implementations
 */
interface ResponseFilterStrategyInterface
{
    /**
     * Execute the algoritm defined by the strategy
     */
    public function algorithm(ResponseInterface $response): ResponseInterface;
}
