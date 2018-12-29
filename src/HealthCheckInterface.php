<?php
namespace Intrepidity\Healthcheck;

interface HealthCheckInterface
{
    /**
     * Add a test to the check
     * @param HealthTestInterface $test
     * @return HealthCheckInterface Reference to self for method chaining
     */
    public function addTest(HealthTestInterface $test): HealthCheckInterface;

    /**
     * Perform all tests in the current check
     * @return HealthReport
     */
    public function performAll(): HealthReport;
}
