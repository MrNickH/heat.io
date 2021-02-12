<?php


namespace Model\Utilities;


class YoutubeAPI
{
    public static function getVideosByChannel($channel){
        $url = 'https://www.googleapis.com/youtube/v3/search?key='.GOOGLE_API.'&channelId='.$channel.'&part=snippet,id&order=date&maxResults=5';
        Network::networkCall($url,'GET',[],"","application/json",false);
        return Network::$responseBody->items;
    }
}