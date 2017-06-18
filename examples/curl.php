<?php

use Palmtree\Curl\Curl;

require dirname(__DIR__) . '/vendor/autoload.php';

$curl = new Curl('http://tests.local/curl.php');

$curl->post(['hello' => 123, 'test' => 'bye']);

var_dump($curl->getResponse()->getHeaders());

print($curl->getResponse()->getBody());

//var_dump( Curl::getContents( 'http://tests.local/curl.php' ) );
