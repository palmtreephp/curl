<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Palmtree\Curl\Curl;

$curl = new Curl([
    'url'       => 'http://example.org',
    'curl_opts' => [
        CURLOPT_FOLLOWLOCATION => true,
    ],
]);

$curl->getRequest()->addHeader('Host', 'example.org');

$response = $curl->execute();

$headers = $response->getHeaders();
$body    = $response->getBody();

var_export($headers);
echo $body;
