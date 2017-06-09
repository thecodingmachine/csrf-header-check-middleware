<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpRequestInterface;

/**
 * This class will check that all POST/PUT/DELETE... requests and verify that the "Origin" of the request is your own website.
 */
final class CsrfHeaderCheckMiddleware implements MiddlewareInterface
{
    /**
     * @var IsSafeHttpRequestInterface
     */
    private $isSafeHttpRequest;

    public function __construct(IsSafeHttpRequestInterface $isSafeHttpRequest)
    {
        $this->isSafeHttpRequest = $isSafeHttpRequest;
    }

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
        $isSafeHttpRequest = $this->isSafeHttpRequest;
        if (!$isSafeHttpRequest($request)) {
            $source = $this->getSourceOrigin($request);
            $target = $this->getTargetOrigin($request);
            if ($source !== $target) {
                throw new CsrfHeaderCheckMiddlewareException('Potential CSRF attack stopped. Source origin and target origin do not match.');
            }
        }
        return $delegate->process($request);
    }

    private function getSourceOrigin(ServerRequestInterface $request): string
    {
        $source = $this->getHeaderLine($request, 'ORIGIN');
        if (null !== $source) {
            return parse_url($source, PHP_URL_HOST);
        }

        $referrer = $this->getHeaderLine($request, 'REFERER');
        if (null === $referrer) {
            throw new CsrfHeaderCheckMiddlewareException('Could not find neither the ORIGIN header nor the REFERER header in the HTTP request.');
        }

        return parse_url($referrer, PHP_URL_HOST);
    }

    private function getTargetOrigin(ServerRequestInterface $request): string
    {
        $host = $this->getHeaderLine($request, 'X-FORWARDED-HOST');
        if (null === $host) {
            $host = $this->getHeaderLine($request, 'HOST');
        }

        if (null === $host) {
            throw new CsrfHeaderCheckMiddlewareException('Could not find the HOST header in the HTTP request.');
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
     * @param string $header
     * @return string|null
     * @throws CsrfHeaderCheckMiddlewareException
     */
    private function getHeaderLine(ServerRequestInterface $request, string $header)
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
