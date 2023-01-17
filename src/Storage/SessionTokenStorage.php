<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Storage;

use Phpolar\CsrfProtection\CsrfToken;

use const Phpolar\CsrfProtection\REQUEST_ID_KEY;

/**
 * Uses the session to store the CSRF token
 * @codeCoverageIgnore
 */
final class SessionTokenStorage extends AbstractTokenStorage
{
    private const COOKIE_SECURE = "cookie_secure";

    private const COOKIE_HTTPONLY = "cookie_httponly";

    private const COOKIE_SAMESITE = "cookie_samesite";

    private const REFERRER_CHECK = "referer_check";

    /**
     * @param array<string,string> $options
     */
    public function __construct(
        private string $requestId = REQUEST_ID_KEY,
        private string $sessionName = "PHPSESSID",
        private array $options = [
            self::COOKIE_SECURE => "1",
            self::COOKIE_HTTPONLY => "1",
            self::COOKIE_SAMESITE => "Strict",
            self::REFERRER_CHECK => "Strict",
        ],
    ) {
        $this->loadFromSession();
    }

    public function __destruct()
    {
        $this->commit();
    }

    public function commit(): void
    {
        $this->startSession();
        $_SESSION[$this->requestId] = $this->queryAll();
    }

    public function queryAll(): array
    {
        return $this->getTokens();
    }

    public function queryOne(int $index): ?CsrfToken
    {
        return $this->getToken($index);
    }

    private function loadFromSession(): void
    {
        $this->startSession();
        $storedTokens = array_filter(
            $_SESSION[$this->requestId] ?? [],
            fn ($it) => $it instanceof CsrfToken,
        );
        array_walk(
            $storedTokens,
            $this->add(...),
        );
    }

    private function startSession(): void
    {
        if (php_sapi_name() !== "cli" && session_status() !== PHP_SESSION_ACTIVE) {
            session_name($this->sessionName);
            session_start($this->options);
        }
    }
}
