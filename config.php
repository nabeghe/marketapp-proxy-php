<?php

declare(strict_types=1);

/**
 * MarketApp Reverse Proxy Configuration
 */
return [
    /**
     * Target base URL of MarketApp API.
     * Default: https://api.marketapp.org
     */
    'target_url' => 'https://api.marketapp.org',

    /**
     * Connection timeout in seconds.
     */
    'connect_timeout' => 15,

    /**
     * Total request execution timeout in seconds.
     */
    'timeout' => 60,

    /**
     * Enable SSL peer and host verification for HTTPS targets.
     */
    'ssl_verify' => true,

    /**
     * Enable debug mode (adds proxy debugging headers and exposes detailed errors).
     */
    'debug' => false,

    /**
     * Forward client IP address via X-Forwarded-* headers.
     */
    'forward_client_ip' => true,

    /**
     * Enable CORS (Cross-Origin Resource Sharing) support.
     */
    'enable_cors' => true,

    /**
     * Allowed origin for CORS headers.
     */
    'cors_origin' => '*',

    /**
     * Optional custom headers to inject into requests sent to MarketApp API.
     */
    'custom_headers' => [
        // 'X-Partner-Id' => 'your-partner-id',
    ],
];
