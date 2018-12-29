<?php
namespace Intrepidity\Healthcheck;

interface HealthServiceInterface
{
    /**
     * Add a check to the set
     * @param CheckInterface $check
     * @return HealthServiceInterface Reference to self for method chaining
     */
    public function addCheck(CheckInterface $check): HealthServiceInterface;

    /**
     * Perform all checks in the current set
     * @return HealthReport
     */
    public function performAll(): HealthReport;
}
