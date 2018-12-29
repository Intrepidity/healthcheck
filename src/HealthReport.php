<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

use JsonSerializable;

class HealthReport implements JsonSerializable
{
    /**
     * @var CheckResult[]
     */
    protected $checkResults;

    /**
     * @param array $checkResults
     */
    public function __construct(array $checkResults)
    {
        array_walk($checkResults, function(CheckResult $result) {
            $this->checkResults[] = $result;
        });
    }

    /**
     * @return float
     */
    public function getTotalDuration(): float
    {
        $totalDuration = 0.0;

        foreach ($this->checkResults as $checkResult)
        {
            $totalDuration += $checkResult->getDuration();
        }

        return $totalDuration;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $checks = [];
        $healthy = true;

        foreach ($this->checkResults as $result)
        {
            if (!$result->isSuccess()) {
                $healthy = false;
            }

            $checks[] = [
                'label' => $result->getLabel(),
                'success' => $result->isSuccess(),
                'duration' => $result->getDuration()
            ];
        }

        return [
            'totalDuration' => $this->getTotalDuration(),
            'healthy' => $healthy,
            'checks' => $checks
        ];
    }

    /**
     * @return CheckResult[]
     */
    public function getCheckResults(): array
    {
        return $this->checkResults;
    }
}
