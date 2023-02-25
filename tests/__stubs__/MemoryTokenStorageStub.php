<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;

use const Phpolar\CsrfProtection\TOKEN_MAX;

final class MemoryTokenStorageStub extends AbstractTokenStorage
{
    public function __construct(private int $maxCount = TOKEN_MAX)
    {
    }

    public function commit(): void
    {
        // noop
    }

    protected function getMaxCount(): int
    {
        return $this->maxCount;
    }

    public function queryAll(): array
    {
        return $this->getTokens();
    }

    public function queryOne(int $index = 0): ?CsrfToken
    {
        return $this->getToken($index);
    }
}
