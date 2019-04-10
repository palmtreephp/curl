<?php

namespace Palmtree\Curl;

class Request
{
    /** @var array */
    private $headers = [];
    /** @var string|null */
    private $body;

    /**
     * @param string $key
     * @param string $value
     *
     * @return self
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return self
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getHeaderStrings()
    {
        $headers = [];
        foreach ($this->getHeaders() as $key => $value) {
            $headers[] = \sprintf('%s: %s', $key, $value);
        }

        return $headers;
    }

    /**
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
