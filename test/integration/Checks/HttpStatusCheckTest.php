<?php
namespace Intrepidity\Healthcheck\IntegrationTests\Checks;

use Intrepidity\Healthcheck\Checks\HttpStatusCheckFactory;
use PHPUnit\Framework\TestCase;
use Zelenin\HttpClient\ClientFactory;
use Zend\Diactoros\RequestFactory;
use Zend\Diactoros\Uri;

class HttpStatusCheckTest extends TestCase
{
    public function testGoogleReturnsSuccess()
    {
        $factory = new HttpStatusCheckFactory(
            (new ClientFactory())->create(),
            new RequestFactory()
        );

        $check = $factory->create(
            "google",
            new Uri("https://www.google.com"),
            [200]
        );

        $result = $check->performCheck();

        $this->assertTrue($result->isSuccess());
        $this->assertGreaterThan(0.0, $result->getDuration());
    }
}
