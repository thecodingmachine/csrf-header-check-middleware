<?php


namespace TheCodingMachine\Middlewares\OriginFetchers;

use Psr\Http\Message\ServerRequestInterface;

class CompositeTargetOrigin implements TargetOriginInterface
{
    /**
     * @var TargetOriginInterface[]
     */
    private $targetOrigins;

    /**
     * @param TargetOriginInterface[] ...$targetOrigins
     */
    public function __construct(TargetOriginInterface ...$targetOrigins)
    {
        $this->targetOrigins = $targetOrigins;
    }

    /**
     * Returns an array of allowed domain names.
     * If the "source" origin matches one of these origins, the request is valid.
     *
     * @return string[]
     */
    public function __invoke(ServerRequestInterface $request): array
    {
        $targetOrigins = [];

        foreach ($this->targetOrigins as $targetOrigin) {
            $targetOrigins = array_merge($targetOrigins, $targetOrigin($request));
        }

        return $targetOrigins;
    }
}
