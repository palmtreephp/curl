<?php

namespace Palmtree\Curl;

use Palmtree\Curl\Exception\BadMethodCallException;
use Palmtree\Curl\Exception\CurlErrorException;

class Curl
{
    /** @var string */
    private $url;
    /** @var resource */
    private $handle;
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;
    /** @var CurlOpts */
    private $curlOpts = [];

    public function __construct(string $url, array $curlOpts = [])
    {
        $this->url     = $url;
        $this->handle  = \curl_init($url);
        $this->request = new Request();

        $this->curlOpts = new CurlOpts($curlOpts);
    }

    /** @throws CurlErrorException */
    public static function getContents(string $url, array $curlOpts = []): string
    {
        $curl = new self($url, $curlOpts);

        return $curl->getResponse()->getBody();
    }

    /**
     * @param string|array $data
     *
     * @throws CurlErrorException
     */
    public function post($data): Response
    {
        $this->getRequest()->setBody($data);

        return $this->execute();
    }

    /**
     * @param string|mixed $json
     *
     * @throws CurlErrorException
     */
    public function postJson($json): Response
    {
        if (!\is_string($json)) {
            $json = \json_encode($json);
        }

        $this->getRequest()->addHeader('Content-Type', 'application/json');
        $this->getRequest()->addHeader('Content-Length', \strlen($json));

        return $this->post($json);
    }

    /** @throws CurlErrorException */
    public function execute(): Response
    {
        if ($this->response !== null) {
            throw new BadMethodCallException('Request already executed');
        }

        \curl_setopt_array($this->handle, $this->curlOpts->toArray());

        if ($headers = $this->getRequest()->getHeaderStrings()) {
            \curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);
        }

        if ($body = $this->getRequest()->getBody()) {
            \curl_setopt($this->handle, CURLOPT_POST, true);
            \curl_setopt($this->handle, CURLOPT_POSTFIELDS, $body);
        }

        $response = \curl_exec($this->handle);

        if ($errorNumber = \curl_errno($this->handle)) {
            throw new CurlErrorException(\curl_error($this->handle), $errorNumber);
        }

        $this->response = new Response($response, \curl_getinfo($this->handle, CURLINFO_HTTP_CODE));

        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @throws CurlErrorException
     */
    public function getResponse(): Response
    {
        if ($this->response === null) {
            $this->execute();
        }

        return $this->response;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCurlOpts(): CurlOpts
    {
        return $this->curlOpts;
    }

    public function __toString(): string
    {
        try {
            return $this->getResponse()->getBody();
        } catch (CurlErrorException $e) {
            return '';
        }
    }
}
