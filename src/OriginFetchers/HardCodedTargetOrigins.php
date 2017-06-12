<?php


namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException;

/**
 * A set of hard coded target origins.
 * To be used if your application is behind a proxy (thus preventing the use of "HostHeader" class.
 */
class HardCodedTargetOrigins implements TargetOriginInterface
{
    /**
     * @var \string[]
     */
    private $origins;

    /**
     * @param \string[] ...$origins A set of domain names for YOUR application.
     */
    public function __construct(string ...$origins)
    {
        $this->origins = $origins;
    }

    /**
     * Returns an array of domain names for your application.
     * If the "source" origin matches one of these origins, the request is valid.
     *
     * @return string[]
     * @throws \TheCodingMachine\Middlewares\CsrfHeaderCheckMiddlewareException
     */
    public function __invoke(ServerRequestInterface $request): array
    {
        return $this->origins;
    }
}
