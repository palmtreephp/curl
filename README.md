# :palm_tree: Palmtree Curl

[![License](http://img.shields.io/packagist/l/palmtree/curl.svg)](LICENSE)
[![Travis](https://img.shields.io/travis/palmtreephp/curl.svg)](https://travis-ci.org/palmtreephp/curl)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/palmtreephp/curl.svg)](https://scrutinizer-ci.com/g/palmtreephp/curl/)
[![Code Coverage](https://scrutinizer-ci.com/g/palmtreephp/curl/badges/coverage.png)](https://scrutinizer-ci.com/g/palmtreephp/curl/)

A PHP cURL wrapper to make HTTP requests easier.

## Requirements
* PHP >= 7.1

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

$contents = Curl::getContents('https://example.org'); 
```

If you want access to the response headers and body, create a new instance instead:

```php
<?php
use Palmtree\Curl\Curl;

$curl = new Curl('https://example.org');

// Returns the response body when used as a string
echo $curl;

$response = $curl->getResponse();

$headers = $response->getHeaders();

$contentType = $response->getHeader('Content-Type');

$body = $response->getBody();
```

### Advanced Usage

```php
<?php
use Palmtree\Curl\Curl;

$curl = new Curl('https://example.org', [
    CURLOPT_FOLLOWLOCATION => true,
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
