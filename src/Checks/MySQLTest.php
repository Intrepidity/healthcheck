<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Intrepidity\Healthcheck\HealthTestInterface;
use Intrepidity\Healthcheck\HealthTestResult;
use Intrepidity\Healthcheck\TestException;

class MySQLTest implements HealthTestInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var callable
     */
    private $connectionFactory;

    /**
     * @param string $hostname
     * @param string $username
     * @param string $password
     * @param int $port
     * @param callable|null $connectionFactory
     * @throws TestException
     */
    public function __construct(string $label, string $hostname, string $username, string $password, int $port = 3306, ?callable $connectionFactory = null)
    {
        if (!in_array('mysql', \PDO::getAvailableDrivers())) {
            throw new TestException("PDO MySQL driver is required for this test");
        }

        $this->label = $label;
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;

        if ($connectionFactory === null) {
            $this->connectionFactory = function() {
                return new \PDO(
                    "mysql:host={$this->hostname};port={$this->port}",
                    $this->username,
                    $this->password,
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                    ]
                );
            };
        } else {
            $this->connectionFactory = $connectionFactory;
        }
    }

    /**
     * @return HealthTestResult
     */
    public function performTest(): HealthTestResult
    {
        $startTime = microtime(true);

        try {
            $connection = call_user_func($this->connectionFactory);
            $connection->query("SELECT 1");

            $success = true;
        }
        catch (\Exception $exception)
        {
            $success = false;
        }
        finally
        {
            return new HealthTestResult(
                $this->label,
                $success,
                microtime(true) - $startTime
            );
        }
    }
}
