<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Intrepidity\Healthcheck\HealthTestInterface;
use Intrepidity\Healthcheck\HealthTestResult;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

class UriStatusTest implements HealthTestInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var array
     */
    private $allowedStatuses;

    /**
     * @var string
     */
    private $label;

    /**
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param UriInterface $uri
     * @param array $allowedStatuses
     * @param string $label
     */
    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriInterface $uri,
        array $allowedStatuses,
        string $label
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uri = $uri;
        $this->allowedStatuses = $allowedStatuses;
        $this->label = $label;
    }

    /**
     * @return HealthTestResult
     */
    public function performTest(): HealthTestResult
    {
        $startTime = microtime(true);

        try
        {
            $response = $this->httpClient->sendRequest(
                $this->requestFactory->createRequest('GET', $this->uri)
            );

            if (in_array($response->getStatusCode(), $this->allowedStatuses)) {
                $success = true;
            }
        }
        catch (ClientExceptionInterface $exception)
        {
            $success = false;
        }

        return new HealthTestResult(
            $this->label,
            $success ?? false,
            (microtime(true) - $startTime)
        );
    }
}
