<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use Phpolar\CsrfProtection\CsrfToken;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;
use const Phpolar\CsrfProtection\TOKEN_MAX;

/**
 * Uses the session to store the CSRF token
 */
final class SessionTokenStorage extends AbstractTokenStorage
{
    public function __construct(
        private AbstractSession $sessionVars,
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
        if ($this->sessionVars->isNotActive() === true) {
            return;
        }
        $this->sessionVars[$this->requestId] = $this->queryAll();
    }

    protected function getMaxCount(): int
    {
        return $this->maxCount;
    }

    /**
     * @return CsrfToken[]
     */
    public function queryAll(): array
    {
        return $this->getTokens();
    }

    public function queryOne(int $index = 0): ?CsrfToken
    {
        return $this->getToken($index);
    }

    private function loadFromSession(): void
    {
        if ($this->sessionVars->isNotActive() === true) {
            return;
        }
        if (isset($this->sessionVars[$this->requestId]) === false) {
            return;
        }
        $storedTokens = $this->sessionVars[$this->requestId];
        array_walk(
            $storedTokens,
            $this->add(...),
        );
    }
}
