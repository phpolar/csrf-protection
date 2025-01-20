<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use ArrayAccess;
use Phpolar\CsrfProtection\CsrfToken;

/**
 * A testable session abstraction.
 *
 * @implements ArrayAccess<int|string,CsrfToken[]>
 */
abstract class AbstractSession implements ArrayAccess
{
    /**
     * @param array<int|string,CsrfToken[]> $sessionVars
     */
    public function __construct(private array &$sessionVars)
    {
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->sessionVars[$offset]) === true;
    }

    /**
     * @param mixed $offset
     * @return CsrfToken[]
     */
    public function offsetGet(mixed $offset): array
    {
        return $this->sessionVars[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->sessionVars[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->sessionVars[$offset]);
    }

    /**
     * Determine if the session is not active.
     */
    abstract public function isNotActive(): bool;
}
