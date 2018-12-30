<?php
namespace Intrepidity\Healthcheck\UnitTests;

use Intrepidity\Healthcheck\HealthReport;
use Intrepidity\Healthcheck\HealthServiceInterface;
use Intrepidity\Healthcheck\ResponseFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class ResponseFactoryTest extends TestCase
{
    public function testCreateReturnsResponse()
    {
        $healthReport = $this->prophesize(HealthReport::class);
        $healthReport->jsonSerialize()->willReturn([]);

        $healthService = $this->prophesize(HealthServiceInterface::class);
        $healthService->performAll()->willReturn($healthReport->reveal());

        $streamFactory = $this->prophesize(StreamFactoryInterface::class);
        $streamFactory->createStream(Argument::exact("[]"))->willReturn(
            $this->prophesize(StreamInterface::class)->reveal()
        );

        $response = $this->prophesize(ResponseInterface::class);
        $response->withHeader(Argument::cetera())->willReturn($response->reveal());
        $response->withBody(Argument::cetera())->willReturn($response->reveal());
        $response->getStatusCode()->willReturn(200);

        $responseFactory = $this->prophesize(ResponseFactoryInterface::class);
        $responseFactory->createResponse(Argument::exact(200))->willReturn(
            $response->reveal()
        );

        $factory = new ResponseFactory(
            $responseFactory->reveal(),
            $streamFactory->reveal(),
            $healthService->reveal()
        );

        $result = $factory->create();

        $this->assertInstanceOf(ResponseInterface::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }
}
