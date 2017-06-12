<?php
namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;

interface SourceOriginInterface
{
    /**
     * Returns the domain name of the website performing the request.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function __invoke(ServerRequestInterface $request): string;
}
