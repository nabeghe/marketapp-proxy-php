<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Tests\Unit;

use Nabeghe\MarketAppProxy\Config;
use Nabeghe\MarketAppProxy\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $config = new Config();

        self::assertSame('https://api.marketapp.org', $config->getTargetUrl());
        self::assertSame(15, $config->getConnectTimeout());
        self::assertSame(60, $config->getTimeout());
        self::assertTrue($config->isSslVerify());
        self::assertFalse($config->isDebug());
        self::assertTrue($config->isForwardClientIp());
        self::assertTrue($config->isEnableCors());
        self::assertSame('*', $config->getCorsOrigin());
    }

    public function testCustomOptions(): void
    {
        $config = new Config([
            'target_url' => 'https://custom-api.example.com/',
            'connect_timeout' => 5,
            'timeout' => 30,
            'ssl_verify' => false,
            'debug' => true,
            'forward_client_ip' => false,
            'enable_cors' => false,
            'cors_origin' => 'https://frontend.example.com',
            'custom_headers' => [
                'X-API-Key' => 'secret123',
            ],
        ]);

        self::assertSame('https://custom-api.example.com', $config->getTargetUrl());
        self::assertSame(5, $config->getConnectTimeout());
        self::assertSame(30, $config->getTimeout());
        self::assertFalse($config->isSslVerify());
        self::assertTrue($config->isDebug());
        self::assertFalse($config->isForwardClientIp());
        self::assertFalse($config->isEnableCors());
        self::assertSame('https://frontend.example.com', $config->getCorsOrigin());
        self::assertArrayHasKey('X-API-Key', $config->getCustomHeaders());
        self::assertSame('secret123', $config->getCustomHeaders()['X-API-Key']);
    }

    public function testHeaderBlacklists(): void
    {
        $config = new Config();

        $requestBlacklist = $config->getRequestHeaderBlacklist();
        self::assertTrue(in_array('host', $requestBlacklist, true));
        self::assertTrue(in_array('transfer-encoding', $requestBlacklist, true));
        self::assertTrue(in_array('connection', $requestBlacklist, true));

        $responseBlacklist = $config->getResponseHeaderBlacklist();
        self::assertTrue(in_array('transfer-encoding', $responseBlacklist, true));
        self::assertTrue(in_array('connection', $responseBlacklist, true));
    }
}
