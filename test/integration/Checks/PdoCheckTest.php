<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\IntegrationTests\Checks;

use Intrepidity\Healthcheck\Checks\PdoCheck;
use PHPUnit\Framework\TestCase;

class PdoCheckTest extends TestCase
{
    public function testConnectsToMysql()
    {
        $check = new PdoCheck(
            "mysql",
            "mysql:host=127.0.0.1;port=3306",
            "root",
            ""
        );

        $result = $check->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testReturnsFailureIfConnectionFails()
    {
        $check = new PdoCheck(
            "mysql",
            "mysql:host=this-host-doesnt-exist;port=3306",
            "root",
            ""
        );

        $result = $check->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }
}
