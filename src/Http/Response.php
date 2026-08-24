<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Http;

/**
 * HTTP Response representation and sender.
 */
class Response
{
    protected int $statusCode;
    /** @var array<string, string|array<string>> */
    protected array $headers = [];
    protected string $body;

    /**
     * @param int $statusCode
     * @param array<string, string|array<string>> $headers
     * @param string $body
     */
    public function __construct(int $statusCode = 200, array $headers = [], string $body = '')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public static function json(array $data, int $statusCode = 200, array $headers = []): self
    {
        $headers['content-type'] = 'application/json; charset=utf-8';
        $body = (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return new self($statusCode, $headers, $body);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return array<string, string|array<string>>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[strtolower($name)] = $value;
        return $this;
    }

    public function addHeader(string $name, string $value): self
    {
        $lower = strtolower($name);
        if (isset($this->headers[$lower])) {
            if (is_array($this->headers[$lower])) {
                $this->headers[$lower][] = $value;
            } else {
                $this->headers[$lower] = [$this->headers[$lower], $value];
            }
        } else {
            $this->headers[$lower] = $value;
        }
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Send response to client output buffer and terminate or return.
     */
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);

            foreach ($this->headers as $name => $values) {
                if (is_array($values)) {
                    foreach ($values as $value) {
                        header($name . ': ' . $value, false);
                    }
                } else {
                    header($name . ': ' . $values, true);
                }
            }
        }

        echo $this->body;
    }
}
