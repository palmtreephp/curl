<?php

namespace Palmtree\Curl;

use Palmtree\ArgParser\ArgParser;

class Curl
{
    /** @var string */
    private $url;
    /** @var resource */
    private $handle;
    /** @var Request $request */
    private $request;
    /** @var Response $response */
    private $response;

    public static $defaults = [
        'curl_opts' => [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_USERAGENT      => 'Palmtree\Curl',
        ],
    ];

    const HTTP_NOT_FOUND = 404;
    const HTTP_OK_MIN    = 200;
    const HTTP_OK_MAX    = 299;

    /**
     * @param array|string $args Array of args or URL.
     */
    public function __construct($args = [])
    {
        $this->args = $this->parseArgs($args);

        $this->handle = \curl_init($this->getUrl());

        // The Response class always parses headers.
        $this->args['curl_opts'][CURLOPT_HEADER] = true;

        \curl_setopt_array($this->handle, $this->args['curl_opts']);

        $this->request = new Request();
    }

    public static function getContents(string $url, array $curlOpts = []): string
    {
        $args = [
            'url'       => $url,
            'curl_opts' => $curlOpts,
        ];

        $curl = new self($args);

        return $curl->getResponse()->getBody();
    }

    /**
     * @param array|string $data
     */
    public function post($data): Response
    {
        $this->getRequest()->setBody($data);

        return $this->execute();
    }

    public function execute(): Response
    {
        $headers = $this->getRequest()->getHeaderStrings();

        if (!empty($headers)) {
            \curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }

        $body = $this->getRequest()->getBody();

        if (!empty($body)) {
            \curl_setopt($this->handle, CURLOPT_POST, true);
            \curl_setopt($this->handle, CURLOPT_POSTFIELDS, $body);
        }

        $response   = \curl_exec($this->handle);
        $statusCode = \curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

        $this->response = new Response($response, $statusCode);

        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        if ($this->response === null) {
            $this->execute();
        }

        return $this->response;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param array|string $args
     *
     * @return array
     */
    protected function parseArgs($args): array
    {
        $parser = new ArgParser($args, 'url');

        $parser->parseSetters($this);

        $args = $parser->resolveOptions(self::$defaults);

        return $args;
    }

    public function __toString(): string
    {
        $body = $this->getResponse()->getBody();

        $body = $body ?: '';

        return $body;
    }

    public function setOpt($key, $value): void
    {
        $this->args['curl_opts'][$key] = $value;

        \curl_setopt($this->handle, $key, $value);
    }
}
