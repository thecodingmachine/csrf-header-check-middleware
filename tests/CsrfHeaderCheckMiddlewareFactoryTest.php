<?php

namespace TheCodingMachine\Middlewares;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class CsrfHeaderCheckMiddlewareFactoryTest extends AbstractMiddlewareTest
{
    public function testFactory()
    {
        $request = new ServerRequest([], [], "http://alice.com/hello", "Get");

        $middleware = CsrfHeaderCheckMiddlewareFactory::createDefault();

        $response = $middleware->process($request, $this->getDelegate());

        $this->assertSame('foobar', (string) $response->getBody());
    }
}
