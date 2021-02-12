<?php

namespace Model\Utilities;


class TimeZone
{
    public static $preferredTZ = "Europe/London";

    public static function setPreferredTZ(String $preference){
        self::$preferredTZ = $preference;
    }

    public static function SetTZ(){
        if(self::GetServerTZ() != self::$preferredTZ) {
            if (self::GetDBTZ() != self::$preferredTZ) {
                if (self::GetDBTZ() != "SYSTEM") {
                    //Database TZ is wrong and can (most likely) be set.
                    $DBTZOutcome = self::SetDBTZ(self::$preferredTZ);
                } else {
                    //Database TZ is wrong and CANNOT be set.
                    $DBTZOutcome = false;
                }
            } else {
                //Database Timezone is RIGHT.
                $DBTZOutcome = true;
            }

            if ($DBTZOutcome) {
                //If the timezone on the DB can be set or is right - set the server TZ.
                self::SetServerTZ(self::$preferredTZ);
            }

        } else {
            //Server TZ is correct.
            if(self::GetDBTZ() != self::$preferredTZ && self::GetDBTZ() != "SYSTEM"){
                //If the DB TZ is not the preffered one or SYSTEM, try to set it.
                $DBTZOutcome = self::SetDBTZ(self::$preferredTZ);
            }

            //If we werent able to set DB Time, and the value WASNT system, try to set the server tZ to match the DB.
            if(!$DBTZOutcome && self::GetDBTZ() != "SYSTEM"){
                self::SetServerTZ(self::GetDBTZ());
            }
        }
    }

    public static function GetServerTZ(){
        return date_default_timezone_get();
    }

    public static function SetServerTZ(String $tz){
        date_default_timezone_set($tz);
    }

    public static function SetDBTZ(String $tz){

        @\CRUD::manualQuery("SET SESSION time_zone = '".$tz."';", false);
        if(self::GetDBTZ() == $tz){
            return true;
        }
        return false;
    }

    public static function GetDBTZ(){
        return \CRUD::manualQuery("SELECT @@session.time_zone")[0]['@@session.time_zone'];
    }
}