<?php

declare(strict_types=1);

/**
 * MarketApp Reverse Proxy Entry Point
 * Compatible with PHP 7.4 to 8.5
 */

// 1. PSR-4 / Standalone Autoloading
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    require_once __DIR__ . '/autoload.php';
}

use Nabeghe\MarketAppProxy\Config;
use Nabeghe\MarketAppProxy\Http\Request;
use Nabeghe\MarketAppProxy\ProxyEngine;

// 2. Load Configuration
$configOptions = [];
$configFile = __DIR__ . '/config.php';
if (file_exists($configFile)) {
    $loadedConfig = require $configFile;
    if (is_array($loadedConfig)) {
        $configOptions = $loadedConfig;
    }
}

try {
    $config = new Config($configOptions);
    $proxy = new ProxyEngine($config);
    $request = Request::capture();

    // 3. Process and Send Proxied Response
    $response = $proxy->handle($request);
    $response->send();
} catch (Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    $debug = !empty($configOptions['debug']) || getenv('MARKETAPP_DEBUG') === 'true';

    echo json_encode([
        'error' => 'InternalProxyError',
        'message' => 'An internal proxy error occurred.',
        'details' => $debug ? [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ] : null,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}
