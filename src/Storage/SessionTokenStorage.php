<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use Phpolar\CsrfProtection\CsrfToken;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\CsrfProtection\TOKEN_MAX;

/**
 * Uses the session to store the CSRF token
 * @codeCoverageIgnore
 */
final class SessionTokenStorage extends AbstractTokenStorage
{
    public function __construct(
        private string $requestId = REQUEST_ID_KEY,
        private int $maxCount = TOKEN_MAX,
    ) {
        $this->loadFromSession();
    }

    public function __destruct()
    {
        $this->commit();
    }

    public function commit(): void
    {
        if ($this->sessionIsNotActive() === true) {
            return;
        }
        $_SESSION[$this->requestId] = $this->queryAll();
    }

    protected function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function queryAll(): array
    {
        $this->loadFromSession();
        return $this->getTokens();
    }

    public function queryOne(int $index = 0): ?CsrfToken
    {
        $this->loadFromSession();
        return $this->getToken($index);
    }

    private function loadFromSession(): void
    {
        if ($this->sessionIsNotActive() === true) {
            return;
        }
        $storedTokens = array_filter(
            $_SESSION[$this->requestId] ?? [],
            fn ($it) => $it instanceof CsrfToken,
        );
        array_walk(
            $storedTokens,
            $this->add(...),
        );
    }

    private function sessionIsNotActive(): bool
    {
        return php_sapi_name() !== "cli" && session_status() !== PHP_SESSION_ACTIVE;
    }
}
