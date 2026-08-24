<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy\Tests\Unit;

use Nabeghe\MarketAppProxy\Http\Response;
use Nabeghe\MarketAppProxy\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function testResponseBasicProperties(): void
    {
        $response = new Response(200, ['content-type' => 'text/html'], '<h1>Hello</h1>');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('<h1>Hello</h1>', $response->getBody());
        self::assertSame(['content-type' => 'text/html'], $response->getHeaders());

        $response->setStatusCode(201);
        $response->setBody('Created');
        $response->setHeader('x-custom', 'abc');

        self::assertSame(201, $response->getStatusCode());
        self::assertSame('Created', $response->getBody());
        self::assertSame('abc', $response->getHeaders()['x-custom']);
    }

    public function testAddHeaderMultipleValues(): void
    {
        $response = new Response(200);
        $response->addHeader('Set-Cookie', 'cookie1=val1');
        $response->addHeader('Set-Cookie', 'cookie2=val2');

        $headers = $response->getHeaders();
        self::assertArrayHasKey('set-cookie', $headers);
        self::assertSame(['cookie1=val1', 'cookie2=val2'], $headers['set-cookie']);
    }

    public function testJsonResponse(): void
    {
        $data = ['success' => true, 'count' => 5];
        $response = Response::json($data, 200, ['x-version' => '1.0']);

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('application/json', $response->getHeaders()['content-type']);
        self::assertSame('1.0', $response->getHeaders()['x-version']);

        $decoded = json_decode($response->getBody(), true);
        self::assertSame($data, $decoded);
    }
}
