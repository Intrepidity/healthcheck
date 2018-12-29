<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpStatusCheckFactory
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
     * @param ClientInterface $httpClient
     * @param RequestFactoryInterface $requestFactory
     */
    public function __construct(ClientInterface $httpClient, RequestFactoryInterface $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param string $label
     * @param UriInterface $uri
     * @param array $allowedStatuses
     * @return HttpStatusCheck
     */
    public function create(string $label, UriInterface $uri, array $allowedStatuses): HttpStatusCheck
    {
        return new HttpStatusCheck(
            $label,
            $this->httpClient,
            $this->requestFactory,
            $uri,
            $allowedStatuses
        );
    }
}
