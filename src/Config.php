<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy;

/**
 * Configuration manager for MarketApp Proxy.
 */
class Config
{
    /**
     * @var string Target base URL of MarketApp API
     */
    protected string $targetUrl = 'https://api.marketapp.org';

    /**
     * @var int Connection timeout in seconds
     */
    protected int $connectTimeout = 15;

    /**
     * @var int Request execution timeout in seconds
     */
    protected int $timeout = 60;

    /**
     * @var bool Whether to verify SSL certificate of the target
     */
    protected bool $sslVerify = true;

    /**
     * @var bool Enable debug response headers / logs
     */
    protected bool $debug = false;

    /**
     * @var bool Add X-Forwarded-* headers
     */
    protected bool $forwardClientIp = true;

    /**
     * @var bool Enable CORS handling
     */
    protected bool $enableCors = true;

    /**
     * @var string Allowed CORS origin
     */
    protected string $corsOrigin = '*';

    /**
     * @var array<string, string> Additional custom headers to send to target
     */
    protected array $customHeaders = [];

    /**
     * Hop-by-hop request headers that should not be forwarded
     * @var array<string>
     */
    protected array $requestHeaderBlacklist = [
        'host',
        'connection',
        'keep-alive',
        'proxy-authenticate',
        'proxy-authorization',
        'te',
        'trailer',
        'transfer-encoding',
        'upgrade',
        'expect',
        'content-length', // Recalculated by cURL
    ];

    /**
     * Hop-by-hop response headers that should not be forwarded back to client
     * @var array<string>
     */
    protected array $responseHeaderBlacklist = [
        'connection',
        'keep-alive',
        'proxy-authenticate',
        'proxy-authorization',
        'te',
        'trailer',
        'transfer-encoding',
        'upgrade',
        'content-encoding', // Handled by web server / cURL decompressed
    ];

    /**
     * Initialize Config with optional options array.
     *
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = [])
    {
        $this->loadFromEnvironment();
        $this->setOptions($options);
    }

    /**
     * Load configurations from PHP environment variables if available.
     */
    public function loadFromEnvironment(): self
    {
        $targetUrl = getenv('MARKETAPP_TARGET_URL');
        if ($targetUrl !== false && trim($targetUrl) !== '') {
            $this->setTargetUrl(trim($targetUrl));
        }

        $timeout = getenv('MARKETAPP_TIMEOUT');
        if ($timeout !== false && is_numeric($timeout)) {
            $this->setTimeout((int) $timeout);
        }

        $connectTimeout = getenv('MARKETAPP_CONNECT_TIMEOUT');
        if ($connectTimeout !== false && is_numeric($connectTimeout)) {
            $this->setConnectTimeout((int) $connectTimeout);
        }

        $sslVerify = getenv('MARKETAPP_SSL_VERIFY');
        if ($sslVerify !== false) {
            $this->setSslVerify(filter_var($sslVerify, FILTER_VALIDATE_BOOLEAN));
        }

        $debug = getenv('MARKETAPP_DEBUG');
        if ($debug !== false) {
            $this->setDebug(filter_var($debug, FILTER_VALIDATE_BOOLEAN));
        }

        $enableCors = getenv('MARKETAPP_ENABLE_CORS');
        if ($enableCors !== false) {
            $this->setEnableCors(filter_var($enableCors, FILTER_VALIDATE_BOOLEAN));
        }

        $corsOrigin = getenv('MARKETAPP_CORS_ORIGIN');
        if ($corsOrigin !== false && trim($corsOrigin) !== '') {
            $this->setCorsOrigin(trim($corsOrigin));
        }

        return $this;
    }

    /**
     * Set multiple options via associative array.
     *
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        if (isset($options['target_url'])) {
            $this->setTargetUrl((string) $options['target_url']);
        }
        if (isset($options['connect_timeout'])) {
            $this->setConnectTimeout((int) $options['connect_timeout']);
        }
        if (isset($options['timeout'])) {
            $this->setTimeout((int) $options['timeout']);
        }
        if (isset($options['ssl_verify'])) {
            $this->setSslVerify((bool) $options['ssl_verify']);
        }
        if (isset($options['debug'])) {
            $this->setDebug((bool) $options['debug']);
        }
        if (isset($options['forward_client_ip'])) {
            $this->setForwardClientIp((bool) $options['forward_client_ip']);
        }
        if (isset($options['enable_cors'])) {
            $this->setEnableCors((bool) $options['enable_cors']);
        }
        if (isset($options['cors_origin'])) {
            $this->setCorsOrigin((string) $options['cors_origin']);
        }
        if (isset($options['custom_headers']) && is_array($options['custom_headers'])) {
            $this->setCustomHeaders($options['custom_headers']);
        }
        if (isset($options['request_header_blacklist']) && is_array($options['request_header_blacklist'])) {
            $this->requestHeaderBlacklist = array_map('strtolower', $options['request_header_blacklist']);
        }
        if (isset($options['response_header_blacklist']) && is_array($options['response_header_blacklist'])) {
            $this->responseHeaderBlacklist = array_map('strtolower', $options['response_header_blacklist']);
        }

        return $this;
    }

    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $url): self
    {
        $this->targetUrl = rtrim($url, '/');
        return $this;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function setConnectTimeout(int $connectTimeout): self
    {
        $this->connectTimeout = max(1, $connectTimeout);
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = max(1, $timeout);
        return $this;
    }

    public function isSslVerify(): bool
    {
        return $this->sslVerify;
    }

    public function setSslVerify(bool $sslVerify): self
    {
        $this->sslVerify = $sslVerify;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    public function isForwardClientIp(): bool
    {
        return $this->forwardClientIp;
    }

    public function setForwardClientIp(bool $forwardClientIp): self
    {
        $this->forwardClientIp = $forwardClientIp;
        return $this;
    }

    public function isEnableCors(): bool
    {
        return $this->enableCors;
    }

    public function setEnableCors(bool $enableCors): self
    {
        $this->enableCors = $enableCors;
        return $this;
    }

    public function getCorsOrigin(): string
    {
        return $this->corsOrigin;
    }

    public function setCorsOrigin(string $corsOrigin): self
    {
        $this->corsOrigin = $corsOrigin;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function getCustomHeaders(): array
    {
        return $this->customHeaders;
    }

    /**
     * @param array<string, string> $customHeaders
     */
    public function setCustomHeaders(array $customHeaders): self
    {
        $this->customHeaders = $customHeaders;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getRequestHeaderBlacklist(): array
    {
        return $this->requestHeaderBlacklist;
    }

    /**
     * @return array<string>
     */
    public function getResponseHeaderBlacklist(): array
    {
        return $this->responseHeaderBlacklist;
    }
}
