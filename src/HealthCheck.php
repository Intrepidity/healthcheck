<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

class HealthCheck implements HealthCheckInterface
{
    /**
     * @var HealthTestInterface[]
     */
    protected $tests;

    /**
     * @param array|null $tests
     */
    public function __construct(array $tests = null)
    {
        foreach ($tests as $test)
        {
            $this->addTest($test);
        }
    }

    /**
     * @param HealthTestInterface $test
     * @return HealthCheckInterface
     */
    public function addTest(HealthTestInterface $test): HealthCheckInterface
    {
        $this->tests[] = $test;

        return $this;
    }

    /**
     * @return HealthReport
     */
    public function performAll(): HealthReport
    {
        $testResults = [];

        foreach ($this->tests as $test)
        {
            $testResults[] = $test->performTest();
        }

        return new HealthReport($testResults);
    }
}
