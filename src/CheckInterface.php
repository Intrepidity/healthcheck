<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

interface CheckInterface
{
    public function performCheck(): CheckResult;
}
