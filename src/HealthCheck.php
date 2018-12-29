<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

class HealthCheck
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
     */
    public function addTest(HealthTestInterface $test)
    {
        $this->tests[] = $test;
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
