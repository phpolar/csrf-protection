<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use DateTimeImmutable;

/**
 * Generates a token used to identify
 * valid requests.
 */
class CsrfTokenGenerator
{
    public function __construct(private int $ttl = TOKEN_DEFAULT_TTL)
    {
    }

    /**
     * Produces the token.
     */
    public function generate(): CsrfToken
    {
        return new CsrfToken(new DateTimeImmutable("now"), $this->ttl);
    }
}
