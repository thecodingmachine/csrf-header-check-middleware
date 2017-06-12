<?php


namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;

/**
 * Reads the target origin from the "Host" HTTP 1.1 header.
 * Note: the "Host" header cannot be modified from Javascript.
 *
 * We do not rely on the "X-Forwarded-Host" header on purpose because this header can be tempered from JS.
 */
class HostHeader implements TargetOriginInterface
{
    use HeaderFetcher;

    /**
     * Returns an array of allowed domain names.
     * If the "source" origin matches one of these origins, the request is valid.
     *
     * @return string[]
     * @throws \TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException
     */
    public function __invoke(ServerRequestInterface $request): array
    {
        $host = $this->getHeaderLine($request, 'HOST');

        if (null === $host) {
            throw new CsrfHeaderCheckMiddlewareException('Could not find the HOST header in the HTTP request.');
        }

        return [ $this->removePortFromHost($host) ];
    }

    private function removePortFromHost(string $host)
    {
        return explode(':', $host)[0];
    }
}
