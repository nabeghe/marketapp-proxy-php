<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Tests;

use Exception;

/**
 * Base test case providing core assertions for standalone test runner and PHPUnit compatibility.
 */
class TestCase
{
    protected static int $assertionCount = 0;

    public static function getAssertionCount(): int
    {
        return self::$assertionCount;
    }

    public static function resetAssertionCount(): void
    {
        self::$assertionCount = 0;
    }

    public function setUp(): void
    {
    }

    public function tearDown(): void
    {
    }

    public static function assertTrue(bool $condition, string $message = ''): void
    {
        self::$assertionCount++;
        if ($condition !== true) {
            throw new Exception($message !== '' ? $message : 'Failed asserting that condition is true.');
        }
    }

    public static function assertFalse(bool $condition, string $message = ''): void
    {
        self::$assertionCount++;
        if ($condition !== false) {
            throw new Exception($message !== '' ? $message : 'Failed asserting that condition is false.');
        }
    }

    public static function assertEquals($expected, $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected != $actual) {
            $msg = $message !== '' ? $message : sprintf('Failed asserting that %s matches expected %s.', json_encode($actual), json_encode($expected));
            throw new Exception($msg);
        }
    }

    public static function assertSame($expected, $actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($expected !== $actual) {
            $msg = $message !== '' ? $message : sprintf('Failed asserting that %s is strictly identical to %s.', json_encode($actual), json_encode($expected));
            throw new Exception($msg);
        }
    }

    public static function assertNull($actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($actual !== null) {
            throw new Exception($message !== '' ? $message : 'Failed asserting that value is null.');
        }
    }

    public static function assertNotNull($actual, string $message = ''): void
    {
        self::$assertionCount++;
        if ($actual === null) {
            throw new Exception($message !== '' ? $message : 'Failed asserting that value is not null.');
        }
    }

    public static function assertCount(int $expectedCount, $countable, string $message = ''): void
    {
        self::$assertionCount++;
        $actualCount = is_countable($countable) ? count($countable) : 0;
        if ($actualCount !== $expectedCount) {
            throw new Exception($message !== '' ? $message : "Failed asserting that count({$actualCount}) matches expected count({$expectedCount}).");
        }
    }

    public static function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        self::$assertionCount++;
        if (strpos($haystack, $needle) === false) {
            throw new Exception($message !== '' ? $message : "Failed asserting that '{$haystack}' contains '{$needle}'.");
        }
    }

    public static function assertArrayHasKey(string $key, array $array, string $message = ''): void
    {
        self::$assertionCount++;
        if (!array_key_exists($key, $array)) {
            throw new Exception($message !== '' ? $message : "Failed asserting that array has key '{$key}'.");
        }
    }
}
