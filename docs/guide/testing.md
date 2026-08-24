# Running Tests

The project includes an automated, zero-dependency unit test suite covering configuration, request capturing, response formatting, and reverse proxy forwarding.

---

## 1. Standalone Test Runner

Run the built-in CLI test runner with no third-party packages required:

```bash
php tests/run.php
```

### Expected Output:

```text
=================================================================
  MarketApp API Reverse Proxy - Test Suite (PHP 8.5.8)
=================================================================

Running ConfigTest:
  [PASS] testDefaultValues
  [PASS] testCustomOptions
  [PASS] testHeaderBlacklists

Running ProxyEngineTest:
  [PASS] testGetConfig
  [PASS] testCorsPreflightHandling
  [PASS] testUnreachableTargetReturnsGatewayError

Running RequestTest:
  [PASS] testRequestProperties
  [PASS] testOptionsMethodDetection
  [PASS] testDefaultValues

Running ResponseTest:
  [PASS] testResponseBasicProperties
  [PASS] testAddHeaderMultipleValues
  [PASS] testJsonResponse

-----------------------------------------------------------------
Results: 12 passed, 0 failed, 12 total (60 assertions) in 1018ms
=================================================================

All tests passed successfully!
```

---

## 2. Running with Composer & PHPUnit

If you have Composer installed:

```bash
# Run tests via composer script
composer test

# Or run PHPUnit directly
./vendor/bin/phpunit
```

---

## 3. Syntax Validation (PHP Lint)

To check the syntax validity of all PHP files in the project:

```bash
php -l index.php
php -l src/Config.php
php -l src/ProxyEngine.php
php -l src/Http/Request.php
php -l src/Http/Response.php
```
