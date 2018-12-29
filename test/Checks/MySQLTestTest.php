<?php
namespace Intrepidity\Healthcheck\Tests\Checks;

use Intrepidity\Healthcheck\Checks\MySQLTest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class MySQLTestTest extends TestCase
{
    public function testSuccessReturnsSuccess()
    {
        $pdoMock = $this->getPdoMock(false);

        $test = new MySQLTest(
            "mysql",
            "some-non-existant-host",
            "root",
            "root",
            3306,
            function() use ($pdoMock) {
                return $pdoMock;
            }
        );

        $result = $test->performTest();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }

    public function testPdoFailureReturnsFailure()
    {
        $pdoMock = $this->getPdoMock(true);

        $test = new MySQLTest(
            "mysql",
            "some-non-existant-host",
            "root",
            "root",
            3306,
            function() use ($pdoMock) {
                return $pdoMock;
            }
        );

        $result = $test->performTest();

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

