<?php

namespace Model\Utilities;


class Network
{
    public static $responseCode;
    public static $responseBody;
    public static $responseHeaders;
    private static $guzzleClient;

    public static function networkCall(
        String $url,
        String $method,
        Array $headers = [],
        $body = "",
        String $contentType = "application/json",
        bool $caching = true,
        int $cacheExpiry = 6000
    ) {
        if ($caching) {
            $cacheHash = md5($url . $method . base64_encode($body) . $contentType . implode($headers));
            $cacheResult = \Cache::checkCache($cacheHash);

            if ($cacheResult) {
                $method = "CACHE";
            };
        }

        if (!self::$guzzleClient) {
            self::$guzzleClient = new \GuzzleHttp\Client([
                'request.options' => [
                    'timeout' => 1,
                    'connect_timeout' => 1
                ]
            ]);
        };

        switch ($method) {
            case "POST":
                if (!isset($headers['Content-Type']) && $contentType) {
                    $headers['Content-Type'] = $contentType;
                }

                if ($headers['Content-Type'] == "application/json") {
                    $body = json_encode($body);
                }

                self::post($url, $headers, $body);
                break;
            case "GET":
                self::get($url, $headers);
                break;
            case "CACHE":
                self::fromCache($cacheResult);
                break;
        }


        if ($caching && !$cacheResult) {
            self::toCache($cacheHash, $cacheExpiry);
        }

    }

    private static function post($url, $headers, $body)
    {
        if ($headers['Content-Type'] == "application/json") {
            $request = self::$guzzleClient->post($url, [
                "headers" => $headers,
                "body" => $body
            ]);
        } else {
            $request = self::$guzzleClient->post($url, [
                "headers" => $headers,
                "form_params" => $body
            ]);
        }

        self::resolveResult($request);
    }

    private static function resolveResult($request)
    {
        self::$responseCode = $request->getStatusCode();
        self::$responseHeaders = $request->getHeaders();
        self::$responseBody = $request->getBody()->getContents();


        if (stristr(self::$responseHeaders['Content-Type'][0], "application/json")) {
            self::$responseBody = json_decode(self::$responseBody);
        }
    }

    private static function get($url, $headers)
    {
        $request = self::$guzzleClient->get($url, [
            "headers" => $headers,
        ]);

        self::resolveResult($request);
    }

    private static function fromCache($cacheData)
    {
        $cacheData = json_decode($cacheData);

        self::$responseCode = $cacheData->statuscode;
        self::$responseHeaders = $cacheData->headers;
        self::$responseBody = $cacheData->responseBody;
    }

    private static function toCache(String $cacheHash, int $expiry)
    {
        \Cache::putCache($cacheHash, json_encode([
            'statuscode' => self::$responseCode,
            'headers' => self::$responseHeaders,
            'responseBody' => self::$responseBody
        ]), $expiry);
    }
}