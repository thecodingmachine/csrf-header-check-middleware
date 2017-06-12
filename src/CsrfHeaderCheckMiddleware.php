<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TheCodingMachine\Middlewares\OriginFetchers\SourceOriginInterface;
use TheCodingMachine\Middlewares\OriginFetchers\TargetOriginInterface;
use TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpRequestInterface;

/**
 * This class will check that all POST/PUT/DELETE... requests and verify that the "Origin" of the request is your own website.
 */
final class CsrfHeaderCheckMiddleware implements MiddlewareInterface
{
    /**
     * @var IsSafeHttpRequestInterface
     */
    private $isSafeHttpRequest;
    /**
     * @var TargetOriginInterface
     */
    private $targetOrigins;

    /**
     * @var SourceOriginInterface
     */
    private $sourceOrigin;

    const STRICT_COMPARE = true;

    /**
     * CsrfHeaderCheckMiddleware constructor.
     * @param IsSafeHttpRequestInterface $isSafeHttpRequest
     * @param TargetOriginInterface $targetOrigins
     * @param SourceOriginInterface $sourceOrigin
     */
    public function __construct(IsSafeHttpRequestInterface $isSafeHttpRequest, TargetOriginInterface $targetOrigins, SourceOriginInterface $sourceOrigin)
    {
        $this->isSafeHttpRequest = $isSafeHttpRequest;
        $this->targetOrigins = $targetOrigins;
        $this->sourceOrigin = $sourceOrigin;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     * @return ResponseInterface
     * @throws CsrfHeaderCheckMiddlewareException
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        $isSafeHttpRequest = $this->isSafeHttpRequest;
        if (!$isSafeHttpRequest($request)) {
            $sourceOrigin = $this->sourceOrigin;
            $targetOrigins = $this->targetOrigins;

            $source = $sourceOrigin($request);
            $targets = $targetOrigins($request);

            if (!in_array($source, $targets, self::STRICT_COMPARE)) {
                throw new CsrfHeaderCheckMiddlewareException('Potential CSRF attack stopped. Source origin and target origin do not match.');
            }
        }
        return $delegate->process($request);
    }
}
