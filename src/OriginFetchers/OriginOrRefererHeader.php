<?php


namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;

/**
 * Fetches data from the Origin header, or from the Referer header if the Origin header is not present
 */
class OriginOrRefererHeader implements SourceOriginInterface
{
    use HeaderFetcher;

    /**
     * Returns the domain name of the website performing the request.
     *
     * @param ServerRequestInterface $request
     * @return string
     * @throws \TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException
     */
    public function __invoke(ServerRequestInterface $request): string
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
}
