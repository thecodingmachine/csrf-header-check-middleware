<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares;

use TheCodingMachine\Middlewares\OriginFetchers\CompositeTargetOrigin;
use TheCodingMachine\Middlewares\OriginFetchers\HardCodedTargetOrigins;
use TheCodingMachine\Middlewares\OriginFetchers\HostHeader;
use TheCodingMachine\Middlewares\OriginFetchers\OriginOrRefererHeader;
use TheCodingMachine\Middlewares\SafeRequests\CompositeIsSafe;
use TheCodingMachine\Middlewares\SafeRequests\IsBypassedCheck;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpMethod;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpRoute;

final class CsrfHeaderCheckMiddlewareFactory
{
    public static function createDefault(array $applicationDomainNames = [], array $safeRoutes = []): CsrfHeaderCheckMiddleware
    {
        return new CsrfHeaderCheckMiddleware(
            new CompositeIsSafe(
                IsSafeHttpMethod::fromDefaultSafeMethods(),
                new IsSafeHttpRoute(...$safeRoutes),
                IsBypassedCheck::fromDefault()
            ),
            new CompositeTargetOrigin(
                new HostHeader(),
                new HardCodedTargetOrigins(...$applicationDomainNames)
            ),
            new OriginOrRefererHeader()
        );
    }
}
