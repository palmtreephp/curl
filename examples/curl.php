<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Palmtree\Curl\Curl;

// Long form
$curl = new Curl('https://example.org', [
    CURLOPT_FOLLOWLOCATION => true,
]);

// Short form
// $curl = new Curl('https://example.org')

$curl->getRequest()->addHeader('Host', 'example.org');

$response = $curl->execute();

$headers = $response->getHeaders();
$body    = $response->getBody();

\var_export($headers);
echo $body;
