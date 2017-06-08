<?php
namespace TheCodingMachine\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This class will check that all POST/DELETE requests and verify that the "Origin" of the request is your own website.
 */
class CsrfHeaderCheckMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws CsrfHeaderCheckMiddlewareException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $method = strtoupper($request->getMethod());
        if (in_array($method, ['POST', 'DELETE'], true)) {
            $source = $this->getSourceOrigin($request);
            $target = $this->getTargetOrigin($request);
            if ($source !== $target) {
                throw new CsrfHeaderCheckMiddlewareException("Potential CSRF attack stopped. Source origin and target origin do not match.");
            }
        }
        return $delegate->process($request);
    }

    private function getSourceOrigin(ServerRequestInterface $request): string
    {
        $source = $this->getHeaderLine($request, 'ORIGIN');
        if ($source === null) {
            $referrer = $this->getHeaderLine($request, 'REFERER');
            if ($referrer === null) {
                throw new CsrfHeaderCheckMiddlewareException("Could not find neither the ORIGIN header nor the REFERER header in the HTTP request.");
            }

            $source = parse_url($referrer, PHP_URL_HOST);
        } else {
            $source = parse_url($source, PHP_URL_HOST);
        }

        return $source;
    }

    private function getTargetOrigin(ServerRequestInterface $request): string
    {
        $host = $this->getHeaderLine($request, 'X-FORWARDED-HOST');
        if ($host === null) {
            $host = $this->getHeaderLine($request, 'HOST');
        }

        if ($host === null) {
            throw new CsrfHeaderCheckMiddlewareException("Could not find the HOST header in the HTTP request.");
        }
        return $this->removePortFromHost($host);
    }

    private function removePortFromHost(string $host)
    {
        return parse_url('http://'.$host, PHP_URL_HOST);
    }

    /**
     * Returns the header, throws an exception if the header is specified more that one in the request.
     * Returns null if nothing found.
     *
     * @param ServerRequestInterface $request
     * @param string $headerLine
     * @return string|null
     * @throws CsrfHeaderCheckMiddlewareException
     */
    private function getHeaderLine(ServerRequestInterface $request, string $headerLine)
    {
        $hosts = $request->getHeader($headerLine);
        if (count($hosts) > 1) {
            throw new CsrfHeaderCheckMiddlewareException("Unexpected request: more than one $headerLine header sent.");
        }
        if (count($hosts) === 1) {
            return $hosts[0];
        }
        return null;
    }
}
