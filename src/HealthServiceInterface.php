<?php
namespace Intrepidity\Healthcheck;

interface HealthServiceInterface
{
    /**
     * Add a test to the check
     * @param CheckInterface $test
     * @return HealthServiceInterface Reference to self for method chaining
     */
    public function addTest(CheckInterface $test): HealthServiceInterface;

    /**
     * Perform all tests in the current check
     * @return HealthReport
     */
    public function performAll(): HealthReport;
}
