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
        $alreadyContains = array_search($token, $this->tokens) !== false;
        $alreadyAtCountThreshold = count($this->tokens) >= $this->getMaxCount();
        if ($alreadyContains === true) {
            return;
        }
        if ($alreadyAtCountThreshold === true) {
            array_shift($this->tokens);
        }
        array_push($this->tokens, $token);
    }

    /**
     * Determines if the storage contains a matching token and if it is valid.
     */
    public function isValid(string $stringToken): bool
    {
        $matchingTokens = array_filter(
            $this->tokens,
            static fn (CsrfToken $token) => $token->represents($stringToken),
        );
        $freshMatchingTokens = array_filter(
            $matchingTokens,
            static fn (CsrfToken $token) => $token->isExpired() === false,
        );
        return count($freshMatchingTokens) > 0;
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
        $this->tokens = [
            // re-index the array
            ...array_filter(
                $this->tokens,
                static fn (CsrfToken $token) => $token->isExpired() === false
            )
        ];
    }

    /**
     * Returns the max count of tokens
     */
    abstract protected function getMaxCount(): int;

    /**
     * Gets all tokens
     */
    protected function getToken(int $index): ?CsrfToken
    {
        $this->clearExpired();
        return $this->tokens[$index] ?? null;
    }

    /**
     * Gets all tokens
     * @return CsrfToken[]
     */
    protected function getTokens(): array
    {
        $this->clearExpired();
        return $this->tokens;
    }
}
