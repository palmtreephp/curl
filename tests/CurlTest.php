<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\Curl;
use Palmtree\Curl\Exception\BadMethodCallException;
use Palmtree\Curl\Exception\InvalidArgumentException;
use Palmtree\Curl\Tests\Fixtures\WebServer;
use PHPUnit\Framework\TestCase;

class CurlTest extends TestCase
{
    /** @var WebServer */
    private $server;

    public function __construct()
    {
        $this->server = new WebServer('localhost', __DIR__ . '/fixtures/server');
        $this->server->start();

        parent::__construct();
    }

    public function __destruct()
    {
        $this->server->stop();
    }

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
        $curl = new Curl($this->server->getUrl('post.php'));

        $response = $curl->post(['foo' => 'bar']);

        $this->assertSame('true', $response->getBody());
    }

    public function testGet()
    {
        $curl = new Curl($this->server->getUrl('get.php'));

        $response = $curl->getResponse();

        $this->assertSame('foo', $response->getBody());
    }

    public function testCannotExecuteMoreThanOnce()
    {
        $this->expectException(BadMethodCallException::class);

        $curl = new Curl($this->server->getUrl());

        $curl->execute();
        $curl->execute();
    }
}