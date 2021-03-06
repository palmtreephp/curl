<?php

namespace Palmtree\Curl\Tests;

use Palmtree\Curl\CurlOpts;
use PHPUnit\Framework\TestCase;

class CurlOptsTest extends TestCase
{
    public function testOffsetExists()
    {
        $opts = new CurlOpts([
            CURLOPT_BINARYTRANSFER => true,
        ]);

        $this->assertTrue(isset($opts[CURLOPT_BINARYTRANSFER]));
    }

    public function testArrayAccess()
    {
        $opts = new CurlOpts([]);

        $opts[CURLOPT_BINARYTRANSFER] = true;

        $this->assertTrue($opts->get(CURLOPT_BINARYTRANSFER));

        unset($opts[CURLOPT_BINARYTRANSFER]);

        $this->assertNull($opts->get(CURLOPT_BINARYTRANSFER));
    }

    public function testArrayIterator()
    {
        $opts = new CurlOpts([
            CURLOPT_BINARYTRANSFER => true,
        ]);

        foreach ($opts as $key => $value) {
            if ($key === CURLOPT_BINARYTRANSFER) {
                $this->assertTrue($value);
            }
        }
    }
}
