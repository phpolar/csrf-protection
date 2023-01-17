<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use Phpolar\CsrfProtection\CsrfToken;

/**
 * Used to store and validate tokens used to mitigate CSRF attacks
 */
abstract class AbstractTokenStorage
{
    /**
     * A collection of CSRF tokens
     *
     * @var CsrfToken[]
     */
    private array $tokens = [];

    /**
     * Adds a token to the storage
     */
    public function add(CsrfToken $token): void
    {
        $this->tokens[] = $token;
    }

    /**
     * Determines if the storage contains the token and if it is valid
     */
    public function isValid(string $stringToken): bool
    {
        return array_reduce(
            $this->tokens,
            static fn (bool $prev, CsrfToken $token) => $prev
                || ($token->represents($stringToken) === true && $token->isExpired() === false),
            false
        );
    }

    /**
     * Store the token between requests
     */
    public abstract function commit(): void;

    /**
     * Returns all tokens
     * @return CsrfToken[]
     */
    public abstract function queryAll(): array;

    /**
     * Returns a single token
     */
    public abstract function queryOne(int $index): ?CsrfToken;

    /**
     * Removes all expired tokens
     */
    public function clearExpired(): void
    {
        $this->tokens = array_filter(
            $this->tokens,
            static fn (CsrfToken $token) => $token->isExpired() === false
        );
    }

    /**
     * Gets all tokens
     * @codeCoverageIgnore
     */
    protected function getToken(int $index): ?CsrfToken
    {
        $this->clearExpired();
        return $this->tokens[$index] ?? null;
    }

    /**
     * Gets all tokens
     * @codeCoverageIgnore
     * @return CsrfToken[]
     */
    protected function getTokens(): array
    {
        $this->clearExpired();
        return $this->tokens;
    }
}
