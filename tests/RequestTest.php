<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetHeaderStrings()
    {
        $headers = [
            'User-Agent'    => 'Palmtree/Curl',
            'Host'          => 'example.org',
            'Cache-Control' => 'no-cache',
        ];

        $request = new Request();

        $request->setHeaders($headers);

        $expected = [
            'User-Agent: Palmtree/Curl',
            'Host: example.org',
            'Cache-Control: no-cache',
        ];

        $this->assertEquals($expected, $request->getHeaderStrings());
    }
}
