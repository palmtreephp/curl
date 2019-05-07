<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testParse()
    {
        $headers = [
            'Date'          => 'Tue, 07 May 2019 11:30:00 GMT',
            'Expires'       => '-1',
            'Cache-Control' => 'private, max-age=0',
            'Content-Type'  => 'text/html; charset=utf-8',
            'Vary'          => 'Accept-Encoding',
        ];

        $rawResponse = 'HTTP/1.1 200 OK' . "\r\n";

        foreach ($headers as $key => $value) {
            $rawResponse .= "$key: $value\r\n";
        }

        $rawResponse .= "\r\nHello, World!";

        $response = new Response($rawResponse);

        foreach ($headers as $key => $value) {
            $this->assertSame($value, $response->getHeader($key));
        }

        $this->assertSame('Hello, World!', $response->getBody());
    }

    public function testParseHttpContinue()
    {
        $headers = [
            'Date'          => 'Tue, 07 May 2019 11:30:00 GMT',
            'Expires'       => '-1',
            'Cache-Control' => 'private, max-age=0',
            'Content-Type'  => 'text/html; charset=utf-8',
            'Vary'          => 'Accept-Encoding',
        ];

        $rawResponse = "HTTP/1.1 100 Continue\r\n\r\n";

        $rawResponse .= "HTTP/1.1 200 OK\r\n";

        foreach ($headers as $key => $value) {
            $rawResponse .= "$key: $value\r\n";
        }

        $rawResponse .= "\r\nHello, World!";

        $response = new Response($rawResponse);

        foreach ($headers as $key => $value) {
            $this->assertSame($value, $response->getHeader($key));
        }

        $this->assertSame('Hello, World!', $response->getBody());
    }

    public function testIsOk()
    {
        $response = new Response('', 200);

        $this->assertTrue($response->isOk());
    }

    public function testIs404()
    {
        $response = new Response('', 404);

        $this->assertTrue($response->is404());
    }

    public function testToString()
    {
        $response = new Response('Hello, World!');

        $this->assertSame('Hello, World!', (string)$response);
    }
}
