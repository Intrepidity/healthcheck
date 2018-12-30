<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\IntegrationTests\Checks;

use Intrepidity\Healthcheck\Checks\PredisCheck;
use PHPUnit\Framework\TestCase;

class PredisCheckTest extends TestCase
{
    public function testConnectsToRedis()
    {
        $check = new PredisCheck(
            "redis",
            [
                "scheme" => "tcp",
                "host" => "localhost",
                "port" => 6379
            ],
            []
        );

        $result = $check->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testReturnsFailureIfConnectionFails()
    {
        $check = new PredisCheck(
            "redis",
            [
                "scheme" => "tcp",
                "host" => "this-host-doesnt-exist",
                "port" => 6379
            ],
            []
        );

        $result = $check->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }
}
