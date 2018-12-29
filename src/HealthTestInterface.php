<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

interface HealthTestInterface
{
    public function performTest(): HealthTestResult;
}
