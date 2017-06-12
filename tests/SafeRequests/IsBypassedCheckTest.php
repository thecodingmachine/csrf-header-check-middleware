<?php

namespace TheCodingMachine\Middlewares\SafeRequests;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

class IsBypassedCheckTest extends \PHPUnit_Framework_TestCase
{
    public function testBypassCheck()
    {
        $check = IsBypassedCheck::fromDefault();

        $request = new ServerRequest([], [], '/', 'GET');

        $this->assertFalse($check($request));

        $request = $request->withAttribute('TheCodingMachine\\BypassCsrf', true);

        $this->assertTrue($check($request));
    }
}
