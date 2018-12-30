<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Intrepidity\Healthcheck\CheckException;
use Intrepidity\Healthcheck\CheckInterface;
use Intrepidity\Healthcheck\CheckResult;

class PredisCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var array
     */
    private $predisParameters;

    /**
     * @var array
     */
    private $predisOptions;

    /**
     * @var callable
     */
    private $predisFactory;

    public function __construct(string $label, array $predisParameters, array $predisOptions, callable $predisFactory = null)
    {
        if (!class_exists('\Predis\Client')) {
            throw new CheckException("predis/predis is required for this check");
        }

        $this->label = $label;
        $this->predisParameters = $predisParameters;
        $this->predisOptions = $predisOptions;

        if ($predisFactory === null) {
            $this->predisFactory = function() {
                return new \Predis\Client(
                    $this->predisParameters,
                    $this->predisOptions
                );
            };
        } else {
            $this->predisFactory = $predisFactory;
        }
    }

    public function performCheck(): CheckResult
    {
        $startTime = microtime(true);

        try {
            /** @var \Predis\Client $connection */
            $connection = call_user_func($this->predisFactory);
            $connection->connect();

            return new CheckResult(
                $this->label,
                $connection->isConnected(),
                microtime(true) - $startTime
            );
        }
        catch (\Predis\PredisException $exception)
        {
            return new CheckResult(
                $this->label,
                false,
                microtime(true) - $startTime
            );
        }
    }
}
