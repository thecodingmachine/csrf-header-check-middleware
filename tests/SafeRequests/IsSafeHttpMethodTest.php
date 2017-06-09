<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpMethod
 */
final class IsSafeHttpMethodTest extends TestCase
{
    /**
     * @dataProvider httpMethodsProvider
     *
     * @param array  $safeMethods
     * @param string $httpMethod
     * @param bool   $expectedResult
     */
    public function testSafeMethods(array $safeMethods, string $httpMethod, bool $expectedResult)
    {
        /* @var $request RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $request->expects(self::any())->method('getMethod')->willReturn($httpMethod);

        self::assertSame($expectedResult, (new IsSafeHttpMethod(...$safeMethods))->__invoke($request));
    }

    public function httpMethodsProvider() : array
    {
        return [
            'empty' => [
                [],
                'GET',
                false,
            ],
            'GET only' => [
                ['GET'],
                'GET',
                true,
            ],
            'get only' => [
                ['get'],
                'GET',
                true,
            ],
            'GET only, matching lowercase get' => [
                ['GET'],
                'get',
                true,
            ],
            'GET only, non-matching method' => [
                ['GET'],
                'PUT',
                false,
            ],
            'GET, PUT only, matching method' => [
                ['GET', 'PUT'],
                'PUT',
                true,
            ],
        ];
    }

    /**
     * @dataProvider safeDefaultsMatchingProvider
     *
     * @param string $httpMethod
     * @param bool   $expectedResult
     */
    public function testSafeMethodsWithDefaults(string $httpMethod, bool $expectedResult)
    {
        /* @var $request RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $request->expects(self::any())->method('getMethod')->willReturn($httpMethod);

        self::assertSame($expectedResult, IsSafeHttpMethod::fromDefaultSafeMethods()->__invoke($request));
    }

    public function safeDefaultsMatchingProvider() : array
    {
        return [
            'empty' => [
                '',
                false,
            ],
            'GET' => [
                'GET',
                true,
            ],
            'get' => [
                'get',
                true,
            ],
            'HEAD' => [
                'HEAD',
                true,
            ],
            'head' => [
                'head',
                true,
            ],
            'OPTIONS' => [
                'OPTIONS',
                true,
            ],
            'options' => [
                'options',
                true,
            ],
            'DELETE' => [
                'DELETE',
                false,
            ],
            'delete' => [
                'delete',
                false,
            ],
            'POST' => [
                'POST',
                false,
            ],
            'post' => [
                'post',
                false,
            ],
            'PUT' => [
                'PUT',
                false,
            ],
            'put' => [
                'put',
                false,
            ],
            'UNKNOWN' => [
                'UNKNOWN',
                false,
            ],
            'unknown' => [
                'unknown',
                false,
            ],
        ];
    }
}
