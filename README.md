# Palmtree Curl

[![License](http://img.shields.io/packagist/l/palmtree/curl.svg)](LICENSE)

A curl class to make http requests a bit easier.

## Requirements
* PHP >= 5.6

## Installation

Use composer to add the package to your dependencies:
```bash
composer require palmtree/curl
```

## Usage

### Basic Usage
```php
<?php
use Palmtree\Curl\Curl;

$curl = new Curl('http://example.org');

// Returns the response body when used as a string
echo $curl;

// Get response headers
$headers = $curl->getResponse()->getHeaders();
// Get body
$body = $curl->getResponse()->getBody();
```

You can use the static `getContents` method if you just want to retreive a response body from a URL:

```php
<?php
use Palmtree\Curl\Curl;

$contents = Curl::getContents('http://example.org'); 
```

### Advanced Usage

```php
<?php
use Palmtree\Curl\Curl;

$curl = new Curl([
    'url' => 'http://example.org',
    'curl_opts' => [
        CURLOPT_FOLLOWLOCATION => true,  
    ],
]);

$curl->getRequest()->addHeader('Host', 'example.org');

$response = $curl->execute();

$headers = $response->getHeaders();
$body = $response->getBody();

```

## License

Released under the [MIT license](LICENSE)
