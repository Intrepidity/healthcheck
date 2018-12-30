<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

use Psr\Log\LoggerInterface;

class HealthService implements HealthServiceInterface
{
    /**
     * @var CheckInterface[]
     */
    protected $checks;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param array|null $checks
     */
    public function __construct(array $checks = null, LoggerInterface $logger = null)
    {
        $this->checks = [];
        $this->logger = $logger ?? new NullLogger();

        if ($checks) {
            foreach ($checks as $check) {
                $this->addCheck($check);
            }
        }
    }

    /**
     * @param CheckInterface $check
     * @return HealthServiceInterface
     */
    public function addCheck(CheckInterface $check): HealthServiceInterface
    {
        $this->checks[] = $check;

        return $this;
    }

    /**
     * @return HealthReport
     */
    public function performAll(): HealthReport
    {
        $checkResults = [];

        foreach ($this->checks as $check)
        {
            $result = $check->performCheck();

            switch ($result->isSuccess()) {
                case true:
                    $this->logger->info("Health check '" . $result->getLabel() . "' is OK. Duration: " . round($result->getDuration(), 2) . "s.");
                    break;
                case false:
                    $this->logger->alert("Health check '" . $result->getLabel() . "' has failed. Duration: " . round($result->getDuration(), 2) . "s.");
                    break;
            }

            $checkResults[] = $result;
        }

        return new HealthReport($checkResults);
    }
}
