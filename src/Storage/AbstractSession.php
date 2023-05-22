<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use ArrayAccess;

/**
 * A testable session abstraction.
 *
 * @implements ArrayAccess<int|string,array<\Phpolar\CsrfProtection\CsrfToken>>
 */
abstract class AbstractSession implements ArrayAccess
{
    /**
     * @param array<int|string,array<\Phpolar\CsrfProtection\CsrfToken>> $sessionVars
     */
    public function __construct(private array &$sessionVars)
    {
    }
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->sessionVars[$offset]) === true;
    }
    public function offsetGet(mixed $offset): mixed
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
