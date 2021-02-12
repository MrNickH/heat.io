<?php

namespace Model\Utilities;


class RandomNumber
{

    public static function getRandomNumber(Int $min, Int $max)
    {
        return self::makeRequest($min, $max);
    }


    private static function makeRequest(Int $min, Int $max, Int $count = 1, Bool $duplicates = true)
    {
        $requestJSON = [
            "jsonrpc" => "2.0",
            "method" => "generateIntegers",
            "params" => [
                "apiKey" => RANDOM_ORG_KEY,
                "n" => $count,
                "min" => $min,
                "max" => $max,
                "replacement" => $duplicates
            ],
            "id" => "somethingRandom"
        ];

        Network::networkCall('https://api.random.org/json-rpc/1/invoke', 'POST', [], $requestJSON);

        if ($count == 1) {
            return Network::$responseBody->result->random->data[0];
        }

        return Network::$responseBody->result->random->data;

    }
}