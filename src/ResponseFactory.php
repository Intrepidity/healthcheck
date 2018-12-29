<?php
declare(strict_types=1);

namespace Intrepidity\Healthcheck;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class ResponseFactory
{
    /**
     * @var ResponseFactoryInterface
     */
    private $psrResponseFactory;

    /**
     * @var HealthServiceInterface
     */
    private $healthService;

    /**
     * @var StreamFactoryInterface
     */
    private $streamFactory;

    /**
     * @param ResponseFactoryInterface $psrResponseFactory
     * @param StreamFactoryInterface $streamFactory
     * @param HealthServiceInterface $healthService
     */
    public function __construct(
        ResponseFactoryInterface $psrResponseFactory,
        StreamFactoryInterface $streamFactory,
        HealthServiceInterface $healthService
    ) {
        $this->psrResponseFactory = $psrResponseFactory;
        $this->streamFactory = $streamFactory;
        $this->healthService = $healthService;
    }

    /**
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        $health = $this->healthService->performAll();
        $body = $this->streamFactory->createStream(json_encode($health));

        $response = $this->psrResponseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Cache-Control', 'no-cache')
            ->withBody($body);

        return $response;
    }
}
