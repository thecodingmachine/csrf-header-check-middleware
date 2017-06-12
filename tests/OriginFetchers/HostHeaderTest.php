<?php

namespace TheCodingMachine\Middlewares\OriginFetchers;

use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;
use Zend\Diactoros\ServerRequest;

class HostHeaderTest extends \PHPUnit_Framework_TestCase
{
    public function testFetchHost()
    {
        $request = new ServerRequest([], [], "http://alice.com:8080/hello", "Post");

        $hostHeader = new HostHeader();

        $hosts = $hostHeader($request);

        $this->assertSame(['alice.com'], $hosts);
    }

    public function testForwardedHostIgnored()
    {
        $request = new ServerRequest([], [], "http://alice.com:8080/hello", "Post");
        $request = $request->withHeader('X-Forwarded-Host', 'eve.com');

        $hostHeader = new HostHeader();

        $hosts = $hostHeader($request);

        $this->assertSame(['alice.com'], $hosts);
    }

    public function testMultipleHostHeaders()
    {
        $request = new ServerRequest([], [], "http://alice.com:8080/hello", "Post");
        $request = $request->withAddedHeader('Host', 'eve.com');

        $hostHeader = new HostHeader();

        $this->expectException(CsrfHeaderCheckMiddlewareException::class);
        $this->expectExceptionMessage("Unexpected request: more than one HOST header sent.");
        $hostHeader($request);
    }
}
