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
     * @param string $label
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param callable|null $connectionFactory
     */
    public function __construct(string $label, string $dsn, string $username, string $password)
    {
        $this->label = $label;
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return CheckResult
     */
    public function performCheck(): CheckResult
    {
        $startTime = microtime(true);

        try {
            $connection = new \PDO(
                $this->dsn,
                $this->username,
                $this->password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                ]
            );
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
