<?php

namespace Palmtree\Curl;

class Curl
{
    public static $defaultCurlOpts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_USERAGENT      => 'Palmtree\Curl',
    ];

    /** @var string */
    private $url;
    /** @var resource */
    private $handle;
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var array */
    private $curlOpts = [];

    const HTTP_NOT_FOUND = 404;
    const HTTP_OK_MIN    = 200;
    const HTTP_OK_MAX    = 299;

    public function __construct(string $url, array $curlOpts = [])
    {
        $this->setUrl($url);

        $this->handle  = \curl_init($url);
        $this->request = new Request();

        $this->buildCurlOpts($curlOpts);
    }

    public static function getContents(string $url, array $curlOpts = []): string
    {
        $curl = new self($url, $curlOpts);

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
        if ($headers = $this->getRequest()->getHeaderStrings()) {
            \curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }

        if ($body = $this->getRequest()->getBody()) {
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

    public function getOpts(): array
    {
        return $this->curlOpts;
    }

    public function setOpt(int $key, $value): self
    {
        if ($key === CURLOPT_HEADER && !$value) {
            throw new \InvalidArgumentException();
        }

        $this->curlOpts[$key] = $value;

        \curl_setopt($this->handle, $key, $value);

        return $this;
    }

    public function __toString(): string
    {
        $body = $this->getResponse()->getBody();

        $body = $body ?: '';

        return $body;
    }

    private function buildCurlOpts(array $curlOpts)
    {
        $this->curlOpts = \array_replace(self::$defaultCurlOpts, $curlOpts);

        // The Response class always parses headers.
        $this->curlOpts[CURLOPT_HEADER] = true;

        \curl_setopt_array($this->handle, $this->curlOpts);
    }
}
