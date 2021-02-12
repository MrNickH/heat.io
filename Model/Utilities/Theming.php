<?php


namespace Model\Utilities;


class Theming
{

    private static $themeInfo = [
        'winter' => [
            'dates' => [
                'start' => '12-1',
                'end' => '12-31'
            ],
            'images' => [
                'logoaccent' => '',
                'footer' => ASSETURL.'/img/theming/footer/winter.png'
            ],
            'js' => 'Snowy()'
        ]
    ];

    public static $theme;

    public static function checkForTheme(){

        if(is_array(self::$theme)){
            return true;
        }

        foreach( self::$themeInfo as $name => $themeData ){
            $startTime = strtotime(date("Y")."-".$themeData['dates']['start']." 00:00:00");
            $endTime = strtotime(date("Y")."-".$themeData['dates']['end']." 23:59:59");

            $time = time();
            if($time < $endTime && $time > $startTime){
                self::$theme = $themeData;
                return true;
            }
        }

        self::$theme = [];
        return false;

    }
}