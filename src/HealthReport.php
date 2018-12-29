<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

use JsonSerializable;

class HealthReport implements JsonSerializable
{
    /**
     * @var CheckResult[]
     */
    protected $testResults;

    /**
     * @param array $testResults
     */
    public function __construct(array $testResults)
    {
        array_walk($testResults, function(CheckResult $result) {
            $this->testResults[] = $result;
        });
    }

    /**
     * @return float
     */
    public function getTotalDuration(): float
    {
        $totalDuration = 0.0;

        foreach ($this->testResults as $testResult)
        {
            $totalDuration += $testResult->getDuration();
        }

        return $totalDuration;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $results = [];
        $healthy = true;

        foreach ($this->testResults as $result)
        {
            if (!$result->isSuccess()) {
                $healthy = false;
            }

            $results[] = [
                'label' => $result->getLabel(),
                'success' => $result->isSuccess(),
                'duration' => $result->getDuration()
            ];
        }

        return [
            'totalDuration' => $this->getTotalDuration(),
            'healthy' => $healthy,
            'results' => $results
        ];
    }

    /**
     * @return CheckResult[]
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }
}
