<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Tests\Unit;

use Nabeghe\MarketAppProxy\Http\Request;
use Nabeghe\MarketAppProxy\Tests\TestCase;

class RequestTest extends TestCase
{
    public function testRequestProperties(): void
    {
        $headers = [
            'authorization' => 'Bearer token123',
            'content-type' => 'application/json',
            'x-custom' => 'custom-value',
        ];

        $request = new Request(
            'post',
            '/api/v1/orders',
            'page=1&limit=10',
            $headers,
            '{"item_id": 42}',
            '192.168.1.50'
        );

        self::assertSame('POST', $request->getMethod());
        self::assertSame('/api/v1/orders', $request->getPath());
        self::assertSame('page=1&limit=10', $request->getQueryString());
        self::assertSame('{"item_id": 42}', $request->getBody());
        self::assertSame('192.168.1.50', $request->getClientIp());
        self::assertFalse($request->isOptions());
        self::assertSame('Bearer token123', $request->getHeader('Authorization'));
        self::assertSame('application/json', $request->getHeader('content-type'));
        self::assertSame('fallback', $request->getHeader('non-existing', 'fallback'));
        self::assertNull($request->getHeader('non-existing'));
    }

    public function testOptionsMethodDetection(): void
    {
        $request = new Request('OPTIONS', '/api/docs');
        self::assertTrue($request->isOptions());
        self::assertSame('OPTIONS', $request->getMethod());
    }

    public function testDefaultValues(): void
    {
        $request = new Request('GET', '/test');
        self::assertSame('GET', $request->getMethod());
        self::assertSame('/test', $request->getPath());
        self::assertSame('', $request->getQueryString());
        self::assertSame('', $request->getBody());
    }
}
