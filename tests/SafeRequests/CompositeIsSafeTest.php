<?php

namespace TheCodingMachine\Middlewares\SafeRequests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class CompositeIsSafeTest extends \PHPUnit_Framework_TestCase
{
    private function getChecker(bool $result): IsSafeHttpRequestInterface
    {
        return new class($result) implements IsSafeHttpRequestInterface {
            /**
             * @var bool
             */
            private $result;

            public function __construct(bool $result)
            {
                $this->result = $result;
            }

            public function __invoke(ServerRequestInterface $request): bool
            {
                return $this->result;
            }
        };
    }

    public function testCompositeAllFalse()
    {
        /* @var $request RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $composite = new CompositeIsSafe($this->getChecker(false), $this->getChecker(false));

        $this->assertFalse($composite($request));
    }

    public function testCompositeOneTrue()
    {
        /* @var $request RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
        $request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();

        $composite = new CompositeIsSafe($this->getChecker(false), $this->getChecker(true));

        $this->assertTrue($composite($request));
    }
}
