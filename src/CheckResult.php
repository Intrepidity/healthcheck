<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

class CheckResult
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $success;

    /**
     * @var float
     */
    protected $duration;

    /**
     * @param bool $success
     * @param float $duration
     */
    public function __construct(string $label, bool $success, float $duration)
    {
        $this->label = $label;
        $this->success = $success;
        $this->duration = $duration;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }
}
