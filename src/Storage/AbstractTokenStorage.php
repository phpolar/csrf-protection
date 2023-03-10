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
        $maxTokens = $this->getMaxCount();
        $this->tokens = count($this->tokens) >= $maxTokens ? [
            ...array_filter(
                $this->tokens,
                static fn ($index) => $index > 0,
                ARRAY_FILTER_USE_KEY,
            ),
            $token
        ] : [...$this->tokens, $token];
    }

    /**
     * Determines if the storage contains the token and if it is valid
     */
    public function isValid(string $stringToken): bool
    {
        foreach ($this->tokens as $token) {
            if ($token->represents($stringToken) === false) {
                continue;
            }
            if ($token->isExpired() === true) {
                continue;
            }
            return true;
        }
        return false;
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
    public abstract function queryOne(int $index = 0): ?CsrfToken;

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
     * Returns the max count of tokens
     */
    abstract protected function getMaxCount(): int;

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
