<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use Psr\Http\Message\ServerRequestInterface;

interface IsSafeHttpRequestInterface
{
    public function __invoke(ServerRequestInterface $request) : bool;
}
