<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Palmtree\Curl\Curl;

$contents = Curl::getContents('https://example.org');

echo $contents;
