<?php

declare(strict_types=1);

/**
 * MarketApp Reverse Proxy - Built-in PSR-4 Autoloader
 *
 * Provides zero-dependency standalone autoloading for Nabeghe\MarketAppProxy.
 * Allows running the proxy out-of-the-box on any PHP 7.4+ hosting without Composer.
 */
spl_autoload_register(function (string $class): void {
    $prefix = 'Nabeghe\\MarketAppProxy\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
