<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares;


use TheCodingMachine\Middlewares\SafeRequests\CompositeIsSafe;
use TheCodingMachine\Middlewares\SafeRequests\IsBypassedCheck;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpMethod;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpRoute;

final class CsrfHeaderCheckMiddlewareFactory
{
    public static function createDefault(array $safeRoutes = []): CsrfHeaderCheckMiddleware
    {
        return new CsrfHeaderCheckMiddleware(
            new CompositeIsSafe(
                IsSafeHttpMethod::fromDefaultSafeMethods(),
                new IsSafeHttpRoute(...$safeRoutes),
                IsBypassedCheck::fromDefault()
            )
        );
    }
}
