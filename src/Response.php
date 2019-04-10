<?php

namespace Palmtree\Curl;

class Response
{
    /** @var array */
    private $headers = [];
    /** @var string */
    private $body;
    /** @var int */
    private $statusCode;

    public function __construct($response = '', $statusCode = 0)
    {
        $this
            ->parse($response)
            ->setStatusCode($statusCode);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getHeader($key)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        return $this->getStatusCode() >= Curl::HTTP_OK_MIN && $this->getStatusCode() <= Curl::HTTP_OK_MAX;
    }

    /**
     * @return bool
     */
    public function is404()
    {
        return $this->getStatusCode() === Curl::HTTP_NOT_FOUND;
    }

    /**
     * @param int $statusCode
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param string $response
     *
     * @return self
     */
    public function parse($response)
    {
        $response = \explode("\r\n\r\n", $response);

        if (\count($response) > 1) {
            // We want the last two parts
            $response = \array_slice($response, -2, 2);

            list($headers, $body) = $response;

            foreach (\explode("\r\n", $headers) as $header) {
                $pair = \explode(': ', $header, 2);

                if (isset($pair[1])) {
                    $this->headers[$pair[0]] = $pair[1];
                }
            }
        } else {
            $body = $response[0];
        }

        $this->body = $body;

        return $this;
    }

    public function __toString()
    {
        $body = $this->getBody();

        $body = $body ?: '';

        return $body;
    }
}
