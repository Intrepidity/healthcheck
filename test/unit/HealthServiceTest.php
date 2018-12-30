<?php
namespace Intrepidity\Healthcheck\UnitTests;

use Intrepidity\Healthcheck\HealthService;
use Intrepidity\Healthcheck\HealthReport;
use Intrepidity\Healthcheck\CheckInterface;
use Intrepidity\Healthcheck\CheckResult;
use PHPUnit\Framework\TestCase;

class HealthServiceTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructThrowsTypeErrorWhenNonTestIsPassed()
    {
        $tests = [
            $this->prophesize(HealthReport::class)->reveal(),  // Invalid object
            $this->prophesize(CheckInterface::class)->reveal() // Valid object
        ];

        new HealthService($tests);
    }

    public function testPerformAllReturnsReport()
    {
        $testMock = $this->prophesize(CheckInterface::class);
        $testMock->performCheck()->willReturn(
            $this->prophesize(CheckResult::class)->reveal()
        );

        $tests = [
            $testMock->reveal(),
            $testMock->reveal()
        ];

        $check = new HealthService($tests);

        $report = $check->performAll();

        $this->assertInstanceOf(HealthReport::class, $report);
        $this->assertCount(2, $report->getCheckResults());
    }
}
