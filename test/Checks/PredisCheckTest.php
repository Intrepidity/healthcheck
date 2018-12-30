<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Tests\Checks;

use Intrepidity\Healthcheck\Checks\PredisCheck;
use PHPUnit\Framework\TestCase;

class PredisCheckTest extends TestCase
{
    public function testSuccessReturnsSuccess()
    {
        $check = new PredisCheck(
            "redis",
            [
                "scheme" => "tcp",
                "host" => "localhost",
                "port" => 6379
            ],
            [],
            function() {
                return $this->getPredisMock();
            }
        );

        $result = $check->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testSoftFailReturnsFailure()
    {
        $check = new PredisCheck(
            "redis",
            [
                "scheme" => "tcp",
                "host" => "localhost",
                "port" => 6379
            ],
            [],
            function() {
                return $this->getPredisMock(false);
            }
        );

        $result = $check->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testExceptionReturnsFailure()
    {
        $check = new PredisCheck(
            "redis",
            [
                "scheme" => "tcp",
                "host" => "localhost",
                "port" => 6379
            ],
            [],
            function() {
                return $this->getPredisMock(false, true);
            }
        );

        $result = $check->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    private function getPredisMock($shouldConnect = true, $shouldThrow = false)
    {
        $predis = $this->prophesize(\Predis\Client::class);

        if ($shouldThrow) {
            $predis->connect()->willThrow(new \Predis\ClientException());
        } else {
            $predis->connect()->shouldBeCalled();
        }

        $predis->isConnected()->willReturn($shouldConnect);

        return $predis->reveal();
    }
}
