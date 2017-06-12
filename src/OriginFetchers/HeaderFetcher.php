<?php


namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;

trait HeaderFetcher
{
    /**
     * Returns the header, throws an exception if the header is specified more that one in the request.
     * Returns null if nothing found.
     *
     * @param ServerRequestInterface $request
     * @param string $header
     * @return string|null
     * @throws CsrfHeaderCheckMiddlewareException
     */
    protected function getHeaderLine(ServerRequestInterface $request, string $header)
    {
        $values = $request->getHeader($header);
        if (count($values) > 1) {
            throw new CsrfHeaderCheckMiddlewareException("Unexpected request: more than one $header header sent.");
        }
        if (count($values) === 1) {
            return $values[0];
        }
        return null;
    }
}
