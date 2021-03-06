<?php
namespace Intrepidity\Healthcheck\UnitTests;

use Intrepidity\Healthcheck\HealthReport;
use Intrepidity\Healthcheck\CheckInterface;
use Intrepidity\Healthcheck\CheckResult;
use PHPUnit\Framework\TestCase;

class HealthReportTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructThrowsTypeErrorWhenNonTestResultIsPassed()
    {
        $results = [
            $this->prophesize(CheckResult::class)->reveal(),   // Valid result
            $this->prophesize(CheckInterface::class)->reveal()      // Invalid result
        ];

        new HealthReport($results);
    }

    public function testGetTotalDurationReturnsTotalDuration()
    {
        $resultMock1 = $this->prophesize(CheckResult::class);
        $resultMock1->getDuration()->willReturn(0.1);

        $resultMock2 = $this->prophesize(CheckResult::class);
        $resultMock2->getDuration()->willReturn(0.2);

        $report = new HealthReport([
            $resultMock1->reveal(),
            $resultMock2->reveal()
        ]);

        $this->assertEquals(0.3, $report->getTotalDuration());
    }

    public function testSerializesToJsonFormat()
    {
        $resultMock1 = $this->prophesize(CheckResult::class);
        $resultMock1->getDuration()->willReturn(0.1);
        $resultMock1->isSuccess()->willReturn(true);
        $resultMock1->getLabel()->willReturn("authentication_api");

        $resultMock2 = $this->prophesize(CheckResult::class);
        $resultMock2->getDuration()->willReturn(0.2);
        $resultMock2->isSuccess()->willReturn(false);
        $resultMock2->getLabel()->willReturn("mysql_database");

        $report = new HealthReport([
            $resultMock1->reveal(),
            $resultMock2->reveal()
        ]);

        $this->assertEquals([
            'checks' => [
                [
                    'label' => 'authentication_api',
                    'success' => true,
                    'duration' => 0.1
                ],
                [
                    'label' => 'mysql_database',
                    'success' => false,
                    'duration' => 0.2
                ]
            ],
            'totalDuration' => 0.3,
            'healthy' => false
        ], $report->jsonSerialize());
    }
}
