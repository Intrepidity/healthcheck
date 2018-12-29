<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck\Checks;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

class UriStatusTestFactory
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
     * @param UriInterface $uri
     * @param array $allowedStatuses
     * @param string $label
     * @return UriStatusTest
     */
    public function create(UriInterface $uri, array $allowedStatuses, string $label): UriStatusTest
    {
        return new UriStatusTest(
            $this->httpClient,
            $this->requestFactory,
            $uri,
            $allowedStatuses,
            $label
        );
    }
}
