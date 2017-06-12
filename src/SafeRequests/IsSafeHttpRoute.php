<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Used to whitelist a set of routes.
 * Those routes will not be checked for CSRF.
 *
 * Useful for routes reserved for server to server APIs (AND already protected in another way).
 */
final class IsSafeHttpRoute implements IsSafeHttpRequestInterface
{
    /**
     * @var \string[]
     */
    private $routes;

    /**
     * @param \string[] ...$routes A list of routes (expressed as regular expressions) that are NOT checked for CSRF.
     */
    public function __construct(string ...$routes)
    {
        $this->routes = $routes;
    }

    public function __invoke(ServerRequestInterface $request) : bool
    {
        $path = $request->getUri()->getPath();

        foreach ($this->routes as $route) {
            if (preg_match($route, $path)) {
                return true;
            }
        }
        return false;
    }
}
