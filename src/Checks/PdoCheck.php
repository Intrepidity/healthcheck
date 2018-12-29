<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Intrepidity\Healthcheck\CheckInterface;
use Intrepidity\Healthcheck\CheckResult;

class PdoCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $dsn;

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
     * @param string $label
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param callable|null $connectionFactory
     */
    public function __construct(string $label, string $dsn, string $username, string $password, ?callable $connectionFactory = null)
    {
        $this->label = $label;
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;

        if ($connectionFactory === null) {
            $this->connectionFactory = function() {
                return new \PDO(
                    $this->dsn,
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
     * @return CheckResult
     */
    public function performTest(): CheckResult
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
            return new CheckResult(
                $this->label,
                $success,
                microtime(true) - $startTime
            );
        }
    }
}
