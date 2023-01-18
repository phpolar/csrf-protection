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
        return $this->getTokens();
    }

    public function queryOne(int $index = 0): ?CsrfToken
    {
        return $this->getToken($index);
    }
}
