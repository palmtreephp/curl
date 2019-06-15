<?php

namespace Palmtree\Curl;

class Request
{
    /** @var array */
    private $headers = [];
    /** @var string|null */
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
        foreach ($this->getHeaders() as $key => $value) {
            $headers[] = "$key: $value";
        }

        return $headers;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }
}
