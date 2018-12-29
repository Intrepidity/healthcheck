<?php
namespace Intrepidity\Healthcheck\Tests\Checks;

use Intrepidity\Healthcheck\Checks\UriStatusTest;
use Intrepidity\Healthcheck\Checks\UriStatusTestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Client\ClientExceptionInterface;

class UriStatusTestTest extends TestCase
{
    public function testAllowedStatusReturnsSuccess()
    {
        $factory = new UriStatusTestFactory(
            $this->getClientInterfaceMock(200),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ],
            "authentication_api"
        );

        $result = $test->performTest();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
        $this->assertEquals("authentication_api", $result->getLabel());
    }

    public function testNonAllowedStatusReturnsFailure()
    {
        $factory = new UriStatusTestFactory(
            $this->getClientInterfaceMock(500),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ],
            "authentication_api"
        );

        $result = $test->performTest();

        $this->assertFalse($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
        $this->assertEquals("authentication_api", $result->getLabel());
    }

    public function testHttpExceptionReturnsFailure()
    {
        $factory = new UriStatusTestFactory(
            $this->getClientInterfaceMock(200, true),
            $this->getRequestFactoryMock()
        );

        $test = $factory->create(
            $this->prophesize(UriInterface::class)->reveal(),
            [
                200,
                201
            ],
            "authentication_api"
        );

        $result = $test->performTest();

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

