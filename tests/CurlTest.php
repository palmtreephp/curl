<?php

namespace Palmtree\Curl\Tests;

use InvalidArgumentException;
use Palmtree\Curl\Curl;
use Palmtree\Curl\Tests\Fixtures\WebServer;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase
{
    public function testCantSetCurlOptHeader()
    {
        $this->expectException(InvalidArgumentException::class);

        $curl = new Curl('https://example.org');

        $curl->setOpt(CURLOPT_HEADER, false);
    }

    public function testDefaultCurlOpts()
    {
        $curl = new Curl('https://example.org');

        $opts = $curl->getOpts();

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $opts);

        $this->assertSame($opts[CURLOPT_USERAGENT], 'Palmtree\Curl');
    }

    public function testCurlOptOverride()
    {
        $curl = new Curl('https://example.org', [
            CURLOPT_USERAGENT => 'Palmtree\Curl\Tests',
        ]);

        $opts = $curl->getOpts();

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $opts);

        $this->assertSame($opts[CURLOPT_USERAGENT], 'Palmtree\Curl\Tests');
    }

    public function testPost()
    {
        $host   = 'localhost';
        $server = new WebServer($host, __DIR__ . '/fixtures/server');
        $port   = $server->start();

        $curl = new Curl("http://$host:$port/post.php");

        $response = $curl->post(['foo' => 'bar']);

        $this->assertSame('true', $response->getBody());

        $server->end();
    }

    public function testGet()
    {
        $host   = 'localhost';
        $server = new WebServer($host, __DIR__ . '/fixtures/server');
        $port   = $server->start();

        $curl = new Curl("http://$host:$port/get.php");

        $response = $curl->getResponse();

        $this->assertSame('foo', $response->getBody());

        $server->end();
    }
}
