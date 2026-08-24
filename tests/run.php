<?php

declare(strict_types=1);

/**
 * Standalone Test Runner for MarketApp Proxy
 * Runs all unit tests with zero external dependencies.
 */

// Autoload project classes
spl_autoload_register(function (string $class) {
    $prefixes = [
        'Nabeghe\\MarketAppProxy\\Tests\\' => __DIR__ . '/',
        'Nabeghe\\MarketAppProxy\\' => __DIR__ . '/../src/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

use Nabeghe\MarketAppProxy\Tests\TestCase;

$startTime = microtime(true);
$testFiles = glob(__DIR__ . '/Unit/*Test.php');

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$failures = [];

echo "\n" . str_repeat('=', 65) . "\n";
echo "  MarketApp API Reverse Proxy - Test Suite (PHP " . PHP_VERSION . ")\n";
echo str_repeat('=', 65) . "\n\n";

foreach ($testFiles as $file) {
    $className = 'Nabeghe\\MarketAppProxy\\Tests\\Unit\\' . basename($file, '.php');
    if (!class_exists($className)) {
        require_once $file;
    }

    if (!class_exists($className)) {
        continue;
    }

    $reflector = new ReflectionClass($className);
    $methods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);

    $testMethods = array_filter($methods, function (ReflectionMethod $method) {
        return strpos($method->getName(), 'test') === 0;
    });

    echo "Running " . $reflector->getShortName() . ":\n";

    foreach ($testMethods as $method) {
        $testName = $method->getName();
        $totalTests++;

        /** @var TestCase $instance */
        $instance = new $className();

        try {
            $instance->setUp();
            $method->invoke($instance);
            $instance->tearDown();

            $passedTests++;
            echo "  \033[32m[PASS]\033[0m {$testName}\n";
        } catch (Throwable $e) {
            $failedTests++;
            $failures[] = [
                'class' => $reflector->getShortName(),
                'method' => $testName,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];
            echo "  \033[31m[FAIL]\033[0m {$testName}\n";
        }
    }
    echo "\n";
}

$executionTime = round((microtime(true) - $startTime) * 1000, 2);
$assertions = TestCase::getAssertionCount();

echo str_repeat('-', 65) . "\n";
echo "Results: {$passedTests} passed, {$failedTests} failed, {$totalTests} total ({$assertions} assertions) in {$executionTime}ms\n";
echo str_repeat('=', 65) . "\n";

if ($failedTests > 0) {
    echo "\n\033[31mFailures:\033[0m\n";
    foreach ($failures as $idx => $failure) {
        echo ($idx + 1) . ") " . $failure['class'] . "::" . $failure['method'] . "\n";
        echo "   " . $failure['message'] . "\n";
        echo "   at " . $failure['file'] . ":" . $failure['line'] . "\n\n";
    }
    exit(1);
}

echo "\n\033[32mAll tests passed successfully!\033[0m\n\n";
exit(0);
