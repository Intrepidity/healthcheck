<?php
namespace Intrepidity\Healthcheck\UnitTests\Checks;

use Intrepidity\Healthcheck\Checks\PdoCheck;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PdoCheckTest extends TestCase
{
    public function testSuccessReturnsSuccess()
    {
        $pdoMock = $this->getPdoMock(false);

        $test = new PdoCheck(
            "mysql",
            "mysql:some-non-existant-host;port=3306",
            "root",
            "root",
            function() use ($pdoMock) {
                return $pdoMock;
            }
        );

        $result = $test->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testPdoFailureReturnsFailure()
    {
        $pdoMock = $this->getPdoMock(true);

        $test = new PdoCheck(
            "mysql",
            "mysql:some-non-existant-host;port=3306",
            "root",
            "root",
            function() use ($pdoMock) {
                return $pdoMock;
            }
        );

        $result = $test->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    private function getPdoMock(bool $shouldThrowException = false)
    {
        $pdo = $this->prophesize(\PDO::class);

        if ($shouldThrowException) {
            $pdo->query(Argument::cetera())->willThrow(new \PDOException());
        }

        return $pdo->reveal();
    }
}

