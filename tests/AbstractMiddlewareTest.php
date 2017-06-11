<?php


namespace TheCodingMachine\Middlewares;


use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\TextResponse;

abstract class AbstractMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    protected function getDelegate() : DelegateInterface
    {
        return new class implements DelegateInterface {

            /**
             * Dispatch the next available middleware and return the response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             */
            public function process(ServerRequestInterface $request)
            {
                return new TextResponse('foobar');
            }
        };
    }
}