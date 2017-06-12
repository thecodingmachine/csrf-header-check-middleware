<?php
namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;

interface TargetOriginInterface
{
    /**
     * Returns an array of allowed domain names.
     * If the "source" origin matches one of these origins, the request is valid.
     *
     * @param ServerRequestInterface $request
     * @return array|\string[]
     */
    public function __invoke(ServerRequestInterface $request): array;
}
