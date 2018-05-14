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
You can use the static `getContents` method if you just want to retrieve a response body from a URL:

```php
<?php
use Palmtree\Curl\Curl;

$contents = Curl::getContents('http://example.org'); 
```

If you want access to the response headers and body, create a new instance instead:

```php
<?php
use Palmtree\Curl\Curl;

$curl = new Curl('http://example.org');

// Returns the response body when used as a string
echo $curl;

$headers = $curl->getResponse()->getHeaders();

$body = $curl->getResponse()->getBody();
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

if($response->is404()) {
    // handle 404 error
}

if($response->isOk()) {
    // response status code is in the 2xx range
}

```

## License

Released under the [MIT license](LICENSE)
