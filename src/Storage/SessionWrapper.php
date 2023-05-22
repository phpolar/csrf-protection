<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

/**
 * Allows for testable session handling.
 */
final class SessionWrapper extends AbstractSession
{
    public function isNotActive(): bool
    {
        return session_status() !== PHP_SESSION_ACTIVE;
    }
}
