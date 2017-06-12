<?php

namespace TheCodingMachine\Middlewares\OriginFetchers;

use PHPUnit\Framework\TestCase;
use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;
use Zend\Diactoros\ServerRequest;

class OriginOrRefererHeaderTest extends TestCase
{
    public function testFailingPostRequestNoOrigin()
    {
        $request = new ServerRequest([], [], "http://alice.com/hello", "Post");

        $headerFetcher = new OriginOrRefererHeader();

        $this->expectException(CsrfHeaderCheckMiddlewareException::class);
        $this->expectExceptionMessage('Could not find neither the ORIGIN header nor the REFERER header in the HTTP request.');

        $headerFetcher($request);
    }

    public function testOriginFetch()
    {
        $request = new ServerRequest([], [], "http://alice.com/hello", "Post");
        $request = $request->withHeader('Origin', 'http://eve.com');

        $headerFetcher = new OriginOrRefererHeader();

        $this->assertSame('eve.com', $headerFetcher($request));
    }

    public function testRefererFetch()
    {
        $request = new ServerRequest([], [], "http://alice.com/hello", "Post");
        $request = $request->withHeader('Referer', 'http://eve.com/foobar?id=42');

        $headerFetcher = new OriginOrRefererHeader();

        $this->assertSame('eve.com', $headerFetcher($request));
    }
}
