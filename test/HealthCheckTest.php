<?php
namespace Intrepidity\Healthcheck\Tests;

use Intrepidity\Healthcheck\HealthCheck;
use Intrepidity\Healthcheck\HealthReport;
use Intrepidity\Healthcheck\HealthTestInterface;
use Intrepidity\Healthcheck\HealthTestResult;
use PHPUnit\Framework\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructThrowsTypeErrorWhenNonTestIsPassed()
    {
        $tests = [
            $this->prophesize(HealthReport::class)->reveal(),       // Invalid object
            $this->prophesize(HealthTestInterface::class)->reveal() // Valid object
        ];

        new HealthCheck($tests);
    }

    public function testPerformAllReturnsReport()
    {
        $testMock = $this->prophesize(HealthTestInterface::class);
        $testMock->performTest()->willReturn(
            $this->prophesize(HealthTestResult::class)->reveal()
        );

        $tests = [
            $testMock->reveal(),
            $testMock->reveal()
        ];

        $check = new HealthCheck($tests);

        $report = $check->performAll();

        $this->assertInstanceOf(HealthReport::class, $report);
        $this->assertCount(2, $report->getTestResults());
    }
}
