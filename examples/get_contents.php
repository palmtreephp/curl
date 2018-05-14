<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Palmtree\Curl\Curl;

$contents = Curl::getContents('http://example.org');

echo $contents;
