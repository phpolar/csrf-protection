<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class ProjectSizeTest extends TestCase
{
    public function thresholds()
    {
        return [
            [(int) PROJECT_SIZE_THRESHOLD]
        ];
    }

    /**
     * @test
     * @dataProvider thresholds()
     * @testdox Source code total size shall be below $threshold bytes
     */
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
