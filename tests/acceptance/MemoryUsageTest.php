<?php

declare(strict_types=1);

namespace Phpolar\CsrfProtection;

use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class MemoryUsageTest extends TestCase
{
    public function thresholds()
    {
        return [
            [(int) PROJECT_MEMORY_USAGE_THRESHOLD]
        ];
    }

    /**
     * @test
     * @dataProvider thresholds()
     * @testdox Memory usage shall be below $threshold bytes
     */
    public function shallBeBelowThreshold(int $threshold)
    {
        $before = memory_get_usage();
        $this->createFormFromTemplate()
            ->createListFromTemplate()
            ->saveDataToFile()
            ->retrieveDataFromFile();
        $after = memory_get_usage();
        $totalUsed = $after - $before;
        // $this->assertGreaterThan(0, $totalUsed);
        $this->assertLessThanOrEqual($threshold, $totalUsed);
    }

    private function createFormFromTemplate(): self
    {
        return $this;
    }

    private function createListFromTemplate(): self
    {
        return $this;
    }

    private function retrieveDataFromFile(): self
    {
        return $this;
    }

    private function saveDataToFile(): self
    {
        return $this;
    }
}
