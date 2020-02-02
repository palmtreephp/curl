<?php

namespace Palmtree\Curl;

class Request
{
    /** @var array */
    private $headers = [];
    /** @var string|array */
    private $body;

    public function addHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = [];

        foreach ($headers as $key => $value) {
            $this->addHeader($key, $value);
        }

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeaderStrings(): array
    {
        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = "$key: $value";
        }

        return $headers;
    }

    /**
     * @param string|array $body
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string|array
     */
    public function getBody()
    {
        return $this->body;
    }
}
