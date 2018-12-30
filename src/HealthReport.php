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
        $this->checkResults = [];

        array_walk($checkResults, function(CheckResult $result): void {
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
     * @return bool
     */
    public function hasOneOrMoreFailedChecks(): bool
    {
        foreach ($this->getCheckResults() as $checkResult)
        {
            if ($checkResult->isSuccess() === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $checks = [];

        foreach ($this->checkResults as $result)
        {
            $checks[] = [
                'label' => $result->getLabel(),
                'success' => $result->isSuccess(),
                'duration' => $result->getDuration()
            ];
        }

        return [
            'totalDuration' => $this->getTotalDuration(),
            'healthy' => $this->hasOneOrMoreFailedChecks() ? false : true,
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
