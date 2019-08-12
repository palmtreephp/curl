<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\Curl;
use Palmtree\Curl\Exception\BadMethodCallException;
use Palmtree\Curl\Exception\InvalidArgumentException;
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
        $server = new WebServer('localhost', __DIR__ . '/fixtures/server');
        $server->start();

        $curl = new Curl($server->getUrl('post.php'));

        $response = $curl->post(['foo' => 'bar']);

        $this->assertSame('true', $response->getBody());

        $server->end();
    }

    public function testGet()
    {
        $server = new WebServer('localhost', __DIR__ . '/fixtures/server');
        $server->start();

        $curl = new Curl($server->getUrl('get.php'));

        $response = $curl->getResponse();

        $this->assertSame('foo', $response->getBody());

        $server->end();
    }

    public function testCannotExecuteMoreThanOnce()
    {
        $this->expectException(BadMethodCallException::class);
        $server = new WebServer('localhost', __DIR__ . '/fixtures/server');

        $curl = new Curl($server->getUrl());

        $curl->execute();
        $curl->execute();
    }
}
