<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

class HealthService implements HealthServiceInterface
{
    /**
     * @var CheckInterface[]
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
     * @param CheckInterface $test
     * @return HealthServiceInterface
     */
    public function addTest(CheckInterface $test): HealthServiceInterface
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
