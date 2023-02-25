<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use DateInterval;
use DateTimeImmutable;

/**
 * Represents a token used to determine the validity ot a request.
 */
final class CsrfToken
{
    private string $token;

    private DateTimeImmutable $expiresOn;

    public function __construct(
        DateTimeImmutable $createdOn,
        int $ttl = 1800,
    ) {
        $this->expiresOn = $createdOn->add(new DateInterval("PT{$ttl}S"));
        $this->token = base64_encode(random_bytes(32));
    }

    /**
     * Determines if the token is expired.
     */
    public function isExpired(): bool
    {
        return new DateTimeImmutable("now") > $this->expiresOn;
    }

    /**
     * Returns the string represented by the this object.
     */
    public function asString(): string
    {
        return $this->token;
    }

    /**
     * Determines if the given string is represented by this object.
     */
    public function represents(string $stringToken): bool
    {
        return $this->token === $stringToken;
    }
}
