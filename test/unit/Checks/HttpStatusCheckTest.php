<?php
namespace Intrepidity\Healthcheck\UnitTests\Checks;

use Intrepidity\Healthcheck\Checks\HttpStatusCheckFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Client\ClientExceptionInterface;

class HttpStatusCheckTest extends TestCase
{
    public function testAllowedStatusReturnsSuccess()
    {
        $factory = new HttpStatusCheckFactory(
            $this->getClientInterfaceMock(200),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            "authentication_api",
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ]
        );

        $result = $test->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
        $this->assertEquals("authentication_api", $result->getLabel());
    }

    public function testNonAllowedStatusReturnsFailure()
    {
        $factory = new HttpStatusCheckFactory(
            $this->getClientInterfaceMock(500),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            "authentication_api",
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ]
        );

        $result = $test->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
        $this->assertEquals("authentication_api", $result->getLabel());
    }

    public function testHttpExceptionReturnsFailure()
    {
        $factory = new HttpStatusCheckFactory(
            $this->getClientInterfaceMock(200, true),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            "authentication_api",
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ]
        );

        $result = $test->performCheck();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
        $this->assertEquals("authentication_api", $result->getLabel());
    }

    private function getClientInterfaceMock(int $desiredStatus, bool $shouldThrowException = false): ClientInterface
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn($desiredStatus);

        $client = $this->prophesize(ClientInterface::class);

        if ($shouldThrowException) {
            $client->sendRequest(Argument::type(RequestInterface::class))->willThrow(
                $this->prophesize(ClientExceptionInterface::class)->reveal()
            );
        } else {
            $client->sendRequest(Argument::type(RequestInterface::class))->willReturn($response);
        }


        return $client->reveal();
    }

    private function getRequestFactoryMock(): RequestFactoryInterface
    {
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $requestFactory->createRequest(Argument::exact('GET'), Argument::type(UriInterface::class))->willReturn(
            $this->prophesize(RequestInterface::class)->reveal()
        );

        return $requestFactory->reveal();
    }
}

