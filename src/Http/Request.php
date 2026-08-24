<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Http;

/**
 * Incoming HTTP Request representation.
 */
class Request
{
    protected string $method;
    protected string $path;
    protected string $queryString;
    /** @var array<string, string> */
    protected array $headers = [];
    protected string $body;
    protected string $clientIp;

    public function __construct(
        ?string $method = null,
        ?string $path = null,
        ?string $queryString = null,
        ?array $headers = null,
        ?string $body = null,
        ?string $clientIp = null
    ) {
        $this->method = $method !== null ? strtoupper(trim($method)) : $this->detectMethod();
        $this->queryString = $queryString ?? ($_SERVER['QUERY_STRING'] ?? '');
        $this->path = $path ?? $this->detectPath();
        $this->headers = $headers ?? $this->detectHeaders();
        $this->body = $body ?? $this->detectBody();
        $this->clientIp = $clientIp ?? $this->detectClientIp();
    }

    /**
     * Create request from PHP globals.
     */
    public static function capture(): self
    {
        return new self();
    }

    protected function detectMethod(): string
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        return strtoupper(trim($method));
    }

    protected function detectPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        
        // Strip query string from URI
        $pos = strpos($requestUri, '?');
        $path = ($pos !== false) ? substr($requestUri, 0, $pos) : $requestUri;
        $path = rawurldecode($path);

        // Normalize base path if script is running in a subdirectory
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $scriptDir = dirname($scriptName);
        
        if ($scriptDir !== '/' && $scriptDir !== '\\' && $scriptDir !== '.' && $scriptDir !== '') {
            $scriptDir = str_replace('\\', '/', $scriptDir);
            if (strpos($path, $scriptDir) === 0) {
                $path = substr($path, strlen($scriptDir));
            }
        }

        // Also check if index.php is directly in the path
        if (strpos($path, '/index.php') === 0) {
            $path = substr($path, strlen('/index.php'));
        }

        $path = '/' . ltrim($path, '/');
        return $path;
    }

    /**
     * Detect all incoming HTTP headers reliably across PHP SAPI environments (Apache, Nginx, LiteSpeed, CLI-server).
     *
     * @return array<string, string>
     */
    protected function detectHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $all = getallheaders();
            if (is_array($all)) {
                foreach ($all as $key => $value) {
                    $headers[strtolower((string) $key)] = (string) $value;
                }
            }
        }

        // Fallback or fill missing headers from $_SERVER
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                if (!isset($headers[$headerName])) {
                    $headers[$headerName] = (string) $value;
                }
            } elseif ($key === 'CONTENT_TYPE' && !isset($headers['content-type'])) {
                $headers['content-type'] = (string) $value;
            } elseif ($key === 'CONTENT_LENGTH' && !isset($headers['content-length'])) {
                $headers['content-length'] = (string) $value;
            } elseif ($key === 'HTTP_AUTHORIZATION' && !isset($headers['authorization'])) {
                $headers['authorization'] = (string) $value;
            } elseif ($key === 'REDIRECT_HTTP_AUTHORIZATION' && !isset($headers['authorization'])) {
                $headers['authorization'] = (string) $value;
            }
        }

        // Fallback for Apache mod_auth digest/basic
        if (!isset($headers['authorization'])) {
            if (isset($_SERVER['PHP_AUTH_USER'])) {
                $pass = $_SERVER['PHP_AUTH_PW'] ?? '';
                $headers['authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['authorization'] = (string) $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

    protected function detectBody(): string
    {
        $input = file_get_contents('php://input');
        return $input !== false ? $input : '';
    }

    protected function detectClientIp(): string
    {
        $headersToCheck = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR',
        ];

        foreach ($headersToCheck as $key) {
            if (!empty($_SERVER[$key])) {
                $ipList = explode(',', (string) $_SERVER[$key]);
                $ip = trim($ipList[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name, ?string $default = null): ?string
    {
        $name = strtolower($name);
        return $this->headers[$name] ?? $default;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getClientIp(): string
    {
        return $this->clientIp;
    }

    public function isOptions(): bool
    {
        return $this->method === 'OPTIONS';
    }
}
