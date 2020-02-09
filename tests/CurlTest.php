<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\Curl;
use Palmtree\Curl\CurlError;
use Palmtree\Curl\Exception\BadMethodCallException;
use Palmtree\Curl\Exception\CurlErrorException;
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

    public function testGetUrl()
    {
        $curl = new Curl('https://example.org');

        $this->assertSame('https://example.org', $curl->getUrl());
    }

    public function testCantSetCurlOptHeader()
    {
        $this->expectException(InvalidArgumentException::class);

        $curl = new Curl('https://example.org');

        $curl->getCurlOpts()->set(CURLOPT_HEADER, false);
    }

    public function testDefaultCurlOpts()
    {
        $curl = new Curl('https://example.org');

        $opts = $curl->getCurlOpts()->toArray();

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $opts);

        $this->assertSame($opts[CURLOPT_USERAGENT], 'Palmtree\Curl');
    }

    public function testCurlOptOverride()
    {
        $curl = new Curl('https://example.org', [
            CURLOPT_USERAGENT => 'Palmtree\Curl\Tests',
        ]);

        $opts = $curl->getCurlOpts()->toArray();

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $opts);

        $this->assertSame($opts[CURLOPT_USERAGENT], 'Palmtree\Curl\Tests');
    }

    public function testCurlSetOpt()
    {
        $curl = new Curl('https://example.org');

        $curl->getCurlOpts()->set(CURLOPT_USERAGENT, 'Palmtree\Curl\Tests');

        $this->assertArrayHasKey(CURLOPT_USERAGENT, $curl->getCurlOpts()->toArray());
        $this->assertSame($curl->getCurlOpts()[CURLOPT_USERAGENT], 'Palmtree\Curl\Tests');
    }

    public function testPost()
    {
        $curl = new Curl($this->server->getUrl('post.php'));

        $response = $curl->post(['foo' => 'bar']);

        $this->assertSame('true', $response->getBody());
    }

    public function testPostJson()
    {
        $curl = new Curl($this->server->getUrl('json.php'));

        $response = $curl->postJson(\json_encode(['foo' => true]));

        $this->assertSame('true', $response->getBody());
    }

    public function testPostArrayAsJson()
    {
        $curl = new Curl($this->server->getUrl('json.php'));

        $response = $curl->postJson(['foo' => true]);

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

    public function testCurlGetContents()
    {
        $contents = Curl::getContents($this->server->getUrl('get.php'));

        $this->assertSame('foo', $contents);
    }

    public function testToString()
    {
        $curl = new Curl($this->server->getUrl('get.php'));

        $this->assertSame('foo', (string)$curl);
    }

    public function testCurlExceptionError()
    {
        $curl = new Curl('foo://bar');

        try {
            $curl->execute();
            $this->fail('Expected exception ' . CurlError::UNSUPPORTED_PROTOCOL . ' not thrown');
        } catch (CurlErrorException $exception) {
            $this->assertSame(CurlError::UNSUPPORTED_PROTOCOL, $exception->getCode());
        }
    }

    public function testCurlExceptionInToString()
    {
        $curl = new Curl('foo://bar');

        $this->assertSame('', (string)$curl);
    }
}
