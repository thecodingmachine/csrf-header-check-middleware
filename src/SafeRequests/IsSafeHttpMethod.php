<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Shamelessly borrowed from Ocramius/PSR7Csrf package.
 */
final class IsSafeHttpMethod implements IsSafeHttpRequestInterface
{
    const STRICT_COMPARE = true; // Used a constant to avoid a Humbug mutation.

    /**
     * @var \string[]
     */
    private $safeMethods;

    public function __construct(string ...$safeMethods)
    {
        $this->safeMethods = array_map('strtoupper', $safeMethods);
    }

    public static function fromDefaultSafeMethods() : self
    {
        return new self('GET', 'HEAD', 'OPTIONS');
    }

    public function __invoke(ServerRequestInterface $request) : bool
    {
        return in_array(strtoupper($request->getMethod()), $this->safeMethods, self::STRICT_COMPARE);
    }
}
