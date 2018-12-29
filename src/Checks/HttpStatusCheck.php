<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Intrepidity\Healthcheck\CheckInterface;
use Intrepidity\Healthcheck\CheckResult;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpStatusCheck implements CheckInterface
{
    /**
     * @var string
     */
    private $label;

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
     * @param string $label
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param UriInterface $uri
     * @param array $allowedStatuses
     */
    public function __construct(
        string $label,
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        UriInterface $uri,
        array $allowedStatuses
    ) {
        $this->label = $label;
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->uri = $uri;
        $this->allowedStatuses = $allowedStatuses;
    }

    /**
     * @return CheckResult
     */
    public function performCheck(): CheckResult
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
        finally
        {
            return new CheckResult(
                $this->label,
                $success ?? false,
                (microtime(true) - $startTime)
            );
        }
    }
}
