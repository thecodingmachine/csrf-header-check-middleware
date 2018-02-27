<?php
declare(strict_types=1);

namespace TheCodingMachine\Middlewares\SafeRequests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @covers \TheCodingMachine\Middlewares\SafeRequests\IsSafeHttpRoute
 */
final class IsSafeHttpRouteTest extends TestCase
{
    /**
     * @dataProvider routesProvider
     *
     * @param array  $routes
     * @param string $path
     * @param bool   $expectedResult
     */
    public function testSafeRoutes(array $routes, string $path, bool $expectedResult)
    {
        /* @var $uri UriInterface|\PHPUnit_Framework_MockObject_MockObject */
        $uri = $this->getMockBuilder(UriInterface::class)->getMock();

        $uri->expects(self::any())->method('getPath')->willReturn($path);

        /* @var $request RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $request->expects(self::any())->method('getUri')->willReturn($uri);

        self::assertSame($expectedResult, (new IsSafeHttpRoute(...$routes))->__invoke($request));
    }

    public function routesProvider() : array
    {
        return [
            'empty' => [
                [],
                '/',
                false,
            ],
            'request one' => [
                ['#/#'],
                '/',
                true,
            ],
            'many routes' => [
                ['#^/foo$#', '#^/bar$#'],
                '/bar',
                true,
            ],
            'many routes' => [
                ['#^/foo$#', '#^/bar$#'],
                '/baz',
                false,
            ],
        ];
    }
}
