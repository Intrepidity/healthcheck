<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

class HealthService implements HealthServiceInterface
{
    /**
     * @var CheckInterface[]
     */
    protected $checks;

    /**
     * @param array|null $checks
     */
    public function __construct(array $checks = null)
    {
        foreach ($checks as $check)
        {
            $this->addCheck($check);
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
            $checkResults[] = $check->performCheck();
        }

        return new HealthReport($checkResults);
    }
}
