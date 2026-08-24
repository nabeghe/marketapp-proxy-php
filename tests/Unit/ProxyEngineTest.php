<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Tests\Unit;

use Nabeghe\MarketAppProxy\Config;
use Nabeghe\MarketAppProxy\Http\Request;
use Nabeghe\MarketAppProxy\ProxyEngine;
use Nabeghe\MarketAppProxy\Tests\TestCase;

class ProxyEngineTest extends TestCase
{
    public function testGetConfig(): void
    {
        $config = new Config(['target_url' => 'https://api.marketapp.org']);
        $engine = new ProxyEngine($config);

        self::assertSame('https://api.marketapp.org', $engine->getConfig()->getTargetUrl());
    }

    public function testCorsPreflightHandling(): void
    {
        $config = new Config([
            'enable_cors' => true,
            'cors_origin' => 'https://app.example.com',
        ]);
        $engine = new ProxyEngine($config);

        $request = new Request(
            'OPTIONS',
            '/api/v1/users',
            '',
            [
                'access-control-request-method' => 'POST',
                'access-control-request-headers' => 'Authorization, Content-Type',
            ]
        );

        $response = $engine->handle($request);

        self::assertSame(204, $response->getStatusCode());
        $headers = $response->getHeaders();
        self::assertArrayHasKey('access-control-allow-origin', $headers);
        self::assertSame('https://app.example.com', $headers['access-control-allow-origin']);
        self::assertSame('POST', $headers['access-control-allow-methods']);
        self::assertSame('Authorization, Content-Type', $headers['access-control-allow-headers']);
    }

    public function testUnreachableTargetReturnsGatewayError(): void
    {
        // Point to a non-routable port or address to verify error encapsulation
        $config = new Config([
            'target_url' => 'http://127.0.0.1:59999',
            'connect_timeout' => 1,
            'timeout' => 1,
            'debug' => true,
        ]);
        $engine = new ProxyEngine($config);

        $request = new Request('GET', '/test');
        $response = $engine->handle($request);

        self::assertTrue(in_array($response->getStatusCode(), [502, 504], true));
        $body = json_decode($response->getBody(), true);
        self::assertSame('GatewayError', $body['error']);
        self::assertSame('Failed to reach upstream MarketApp API target.', $body['message']);
    }
}
