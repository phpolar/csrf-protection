<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use Generator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[CoversNothing]
final class ProjectSizeTest extends TestCase
{
    public static function thresholds(): Generator
    {
        yield [(int) PROJECT_SIZE_THRESHOLD];
    }

    #[Test]
    #[TestDox("Source code total size shall be below \$threshold bytes")]
    #[DataProvider("thresholds")]
    public function shallBeBelowThreshold(int $threshold)
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
        $this->assertLessThanOrEqual($threshold, $totalSize);
    }
}
