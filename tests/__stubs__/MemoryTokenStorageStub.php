<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection\Tests\Stubs;

use Phpolar\CsrfProtection\CsrfToken;
use Phpolar\CsrfProtection\Storage\AbstractTokenStorage;

final class MemoryTokenStorageStub extends AbstractTokenStorage
{
    public function commit(): void
    {
        // noop
    }

    public function queryAll(): array
    {
        // noop
        return $this->getTokens();
    }

    public function queryOne(int $index): ?CsrfToken
    {
        // noop
        return $this->getToken($index);
    }
}
