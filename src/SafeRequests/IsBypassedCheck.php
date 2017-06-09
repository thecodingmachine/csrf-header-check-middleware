<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Checks for the presence of a 'TheCodingMachine\BypassCsrf' attribute in the request.
 */
final class IsBypassedCheck implements IsSafeHttpRequestInterface
{

    /**
     * @var string
     */
    private $attributeName;

    public function __construct(string $attributeName)
    {
        $this->attributeName = $attributeName;
    }

    public static function fromDefault() : self
    {
        return new self('TheCodingMachine\\BypassCsrf');
    }

    public function __invoke(ServerRequestInterface $request) : bool
    {
        return (bool) $request->getAttribute($this->attributeName);
    }
}
