<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;


use Psr\Http\Message\ServerRequestInterface;

final class CompositeIsSafe implements IsSafeHttpRequestInterface
{
    /**
     * @var IsSafeHttpRequestInterface[]
     */
    private $isSafeHttpRequests;

    public function __construct(IsSafeHttpRequestInterface ...$isSafeHttpRequests)
    {
        $this->isSafeHttpRequests = $isSafeHttpRequests;
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        foreach ($this->isSafeHttpRequests as $isSafeHttpRequest) {
            if ($isSafeHttpRequest($request)) {
                return true;
            }
        }
        return false;
    }
}