<?php

namespace Palmtree\Curl;

class Response
{
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_OK_MIN    = 200;
    public const HTTP_OK_MAX    = 299;

    /** @var array */
    private $headers = [];
    /** @var string */
    private $body;
    /** @var int */
    private $statusCode;

    public function __construct(string $response = '', int $statusCode = 0)
    {
        $this->parse($response);
        $this->statusCode = $statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isOk(): bool
    {
        return $this->statusCode >= self::HTTP_OK_MIN && $this->statusCode <= self::HTTP_OK_MAX;
    }

    public function is404(): bool
    {
        return $this->statusCode === self::HTTP_NOT_FOUND;
    }

    public function __toString(): string
    {
        return $this->body;
    }

    private function parse(string $response)
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
    }
}
