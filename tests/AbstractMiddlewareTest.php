<?php


namespace TheCodingMachine\Middlewares;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

abstract class AbstractMiddlewareTest extends TestCase
{
    protected function getDelegate() : RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {

            /**
             * Dispatch the next available middleware and return the response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             */
            public function handle(ServerRequestInterface $request):ResponseInterface
            {
                return new TextResponse('foobar');
            }
        };
    }
}
