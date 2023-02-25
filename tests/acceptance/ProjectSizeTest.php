<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[CoversNothing]
final class ProjectSizeTest extends TestCase
{
    #[Test]
    #[TestDox("Source code total size shall be below " . PROJECT_SIZE_THRESHOLD . " bytes")]
    public function shallBeBelowThreshold()
    {
        $totalSize = array_reduce(
            array_map(
                strlen(...),
                preg_replace(
                    "/\/\*.*\*\//s",
                    "",
                    array_map(
                        file_get_contents(...),
                        glob(getcwd() . SRC_GLOB, GLOB_BRACE),
                    ),
                ),
            ),
            fn (int $soFar, int $fSize) => $soFar + $fSize,
            0
        );
        $this->assertGreaterThan(0, $totalSize);
        $this->assertLessThanOrEqual((int) PROJECT_SIZE_THRESHOLD, $totalSize);
    }
}
