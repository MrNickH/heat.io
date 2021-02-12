<?php

namespace Model\Utilities;


class TwitchAPI
{

    private static $bearertoken;

    public static function getStreamers($usernames = [])
    {
        if (!self::$bearertoken) {
            self::setupBearerToken();
        }

        return [];

        Network::networkCall('https://api.twitch.tv/helix/users?login=' . implode('&login=', $usernames), 'GET',
            ['Authorization' => 'Bearer ' . self::$bearertoken]);

        return Network::$responseBody->data;
    }

    private static function setupBearerToken()
    {
        Network::networkCall('https://id.twitch.tv/oauth2/token?client_id=' . TWITCH_CLIENT_ID . '&client_secret=' . TWITCH_SECRET . '&grant_type=client_credentials&scope=',
            'POST');
        self::$bearertoken = Network::$responseBody->access_token;
    }

    public static function getStreamData(String $username)
    {
        
        return null;
        if (!self::$bearertoken) {
            self::setupBearerToken();
        }


        return [];

        Network::networkCall('https://api.twitch.tv/helix/streams?user_login=' . $username, 'GET',
            ['Client-ID' => TWITCH_CLIENT_ID], '','application/json', true, 300);

        $resp = Network::$responseBody;

        if(is_string($resp)){
            $resp = json_decode($resp);
        } elseif(is_array($resp)) {
            $resp = json_decode(json_encode($resp));
        }

        return $resp->data;
    }

}