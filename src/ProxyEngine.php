<?php

declare(strict_types=1);

namespace Nabeghe\MarketAppProxy;

use Nabeghe\MarketAppProxy\Http\Request;
use Nabeghe\MarketAppProxy\Http\Response;

/**
 * Main reverse proxy engine.
 */
class ProxyEngine
{
    protected Config $config;

    public function __construct(?Config $config = null)
    {
        $this->config = $config ?? new Config();
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Handle incoming request and return response.
     */
    public function handle(?Request $request = null): Response
    {
        $startTime = microtime(true);
        $request = $request ?? Request::capture();

        // Handle CORS Preflight request
        if ($this->config->isEnableCors() && $request->isOptions()) {
            return $this->handleCorsPreflight($request);
        }

        // Build target destination URL
        $targetUrl = $this->buildTargetUrl($request);

        // Prepare cURL handle
        $ch = curl_init();
        if ($ch === false) {
            return Response::json([
                'error' => 'ProxyError',
                'message' => 'Failed to initialize cURL.',
            ], 500);
        }

        $responseHeaders = [];

        // Build request headers for target
        $forwardHeaders = $this->prepareForwardHeaders($request);

        curl_setopt($ch, CURLOPT_URL, $targetUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->config->getConnectTimeout());
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->config->getTimeout());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->config->isSslVerify());
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->config->isSslVerify() ? 2 : 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        // Capture response headers cleanly
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($curlHandle, string $headerLine) use (&$responseHeaders) {
            $len = strlen($headerLine);
            $header = trim($headerLine);
            if ($header === '' || stripos($header, 'HTTP/') === 0) {
                return $len;
            }

            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $name = strtolower(trim($parts[0]));
                $value = trim($parts[1]);
                $responseHeaders[$name][] = $value;
            }

            return $len;
        });

        // Attach request body if present or applicable
        $method = $request->getMethod();
        if ($method === 'HEAD') {
            curl_setopt($ch, CURLOPT_NOBODY, true);
        } elseif (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], true)) {
            $body = $request->getBody();
            if ($body !== '') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        }

        // Execute request
        $responseBody = curl_exec($ch);
        $executionTimeMs = round((microtime(true) - $startTime) * 1000, 2);

        if (curl_errno($ch)) {
            $curlError = curl_error($ch);
            $curlErrno = curl_errno($ch);
            if (is_resource($ch)) {
                curl_close($ch);
            }

            $statusCode = ($curlErrno === CURLE_OPERATION_TIMEDOUT || $curlErrno === CURLE_OPERATION_TIMEOUTED) ? 504 : 502;

            $errorPayload = [
                'error' => 'GatewayError',
                'message' => 'Failed to reach upstream MarketApp API target.',
                'target_url' => $this->config->isDebug() ? $targetUrl : null,
                'curl_error' => $this->config->isDebug() ? $curlError : null,
                'curl_errno' => $this->config->isDebug() ? $curlErrno : null,
            ];

            return Response::json(array_filter($errorPayload, fn($v) => $v !== null), $statusCode);
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statusCode === 0) {
            $statusCode = 200;
        }

        if (is_resource($ch)) {
            curl_close($ch);
        }

        // Filter and compile outgoing response headers
        $blacklist = $this->config->getResponseHeaderBlacklist();
        $finalHeaders = [];

        foreach ($responseHeaders as $name => $values) {
            if (in_array(strtolower($name), $blacklist, true)) {
                continue;
            }

            if (count($values) === 1) {
                $finalHeaders[$name] = $values[0];
            } else {
                $finalHeaders[$name] = $values;
            }
        }

        // Apply CORS headers if enabled
        if ($this->config->isEnableCors()) {
            $finalHeaders['access-control-allow-origin'] = $this->config->getCorsOrigin();
            $finalHeaders['access-control-allow-credentials'] = 'true';
        }

        // Add debug headers if debug mode is active
        if ($this->config->isDebug()) {
            $finalHeaders['x-proxy-by'] = 'Nabeghe-MarketApp-Proxy-PHP';
            $finalHeaders['x-proxy-target'] = $targetUrl;
            $finalHeaders['x-proxy-time-ms'] = (string) $executionTimeMs;
        }

        return new Response($statusCode, $finalHeaders, is_string($responseBody) ? $responseBody : '');
    }

    /**
     * Build destination target URL with path and query parameters.
     */
    protected function buildTargetUrl(Request $request): string
    {
        $base = $this->config->getTargetUrl();
        $path = $request->getPath();
        $query = $request->getQueryString();

        $url = $base . $path;
        if ($query !== '') {
            $url .= '?' . $query;
        }

        return $url;
    }

    /**
     * Prepare headers to send to target server.
     *
     * @return array<string>
     */
    protected function prepareForwardHeaders(Request $request): array
    {
        $blacklist = $this->config->getRequestHeaderBlacklist();
        $incomingHeaders = $request->getHeaders();
        $forward = [];

        foreach ($incomingHeaders as $name => $value) {
            if (in_array(strtolower($name), $blacklist, true)) {
                continue;
            }
            $forward[$name] = $value;
        }

        // Add X-Forwarded headers
        if ($this->config->isForwardClientIp()) {
            $clientIp = $request->getClientIp();
            if (isset($forward['x-forwarded-for'])) {
                $forward['x-forwarded-for'] .= ', ' . $clientIp;
            } else {
                $forward['x-forwarded-for'] = $clientIp;
            }

            if (!isset($forward['x-forwarded-proto'])) {
                $forward['x-forwarded-proto'] = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            }

            if (!isset($forward['x-forwarded-host']) && isset($_SERVER['HTTP_HOST'])) {
                $forward['x-forwarded-host'] = (string) $_SERVER['HTTP_HOST'];
            }
        }

        // Append custom user headers
        foreach ($this->config->getCustomHeaders() as $name => $val) {
            $forward[strtolower($name)] = $val;
        }

        // Convert to array of "Header-Name: Value"
        $headerLines = [];
        foreach ($forward as $name => $val) {
            // Capitalize header name nicely for target compatibility
            $formattedName = implode('-', array_map('ucfirst', explode('-', $name)));
            $headerLines[] = $formattedName . ': ' . $val;
        }

        return $headerLines;
    }

    /**
     * Handle CORS Preflight OPTIONS requests.
     */
    protected function handleCorsPreflight(Request $request): Response
    {
        $requestHeaders = $request->getHeader('access-control-request-headers', '*');
        $requestMethod = $request->getHeader('access-control-request-method', 'GET, POST, PUT, DELETE, PATCH, OPTIONS, HEAD');

        $headers = [
            'access-control-allow-origin' => $this->config->getCorsOrigin(),
            'access-control-allow-methods' => $requestMethod,
            'access-control-allow-headers' => $requestHeaders,
            'access-control-allow-credentials' => 'true',
            'access-control-max-age' => '86400',
            'content-length' => '0',
            'content-type' => 'text/plain',
        ];

        return new Response(204, $headers, '');
    }
}
