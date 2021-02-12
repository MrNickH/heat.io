<?php

class StringFunctions
{
    public static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle)
    {

        $needle = str_replace("\r", "\~@######", $needle);
        $needle = str_replace("~@######", "r", $needle);

        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }


        return (substr($haystack, -$length) == $needle);
    }

    public static function removeAllBut($what, $string)
    {
        switch ($what) {
            case "lowercase":
                return preg_replace("/[^a-z]/", "", $string);
            case "uppercase":
                return preg_replace("/[^A-Z]/", "", $string);
            case "numeric":
                return preg_replace("/[^0-9]/", "", $string);
            case "symbolic":
                return preg_replace("/[0-9a-zA-Z]/", "", $string);
            default:
                return $string;
        }
    }

    public function removeSpecials($string){
        $specialsRemoved = preg_replace('/[^a-z0-9 {},%*:\\\/\-\[\]]/', '', $string);
    }

    public static function stringContains($hs, $n)
    {
        return (strpos(strtolower($hs), strtolower($n)) !== false);
    }

    public static function removeEmpties($array)
    {
        foreach ($array as $key => $value) {
            if ($value == "") {
                unset($array[$key]);
            }
        }
        return $array;
    }

    public static function findInArrayValues($array, $find)
    {
        foreach ($array as $v) {
            if (preg_match("/\b$find\b/i", $v)) {
                return true;
            }
        }
    }

    public static function entities( $string ) {
        $stringBuilder = "";
        $offset = 0;

        if ( empty( $string ) ) {
            return "";
        }

        while ( $offset >= 0 ) {
            $decValue = self::ordutf8( $string, $offset );
            $char = self::unichr($decValue);

            $htmlEntited = htmlentities( $char );
            if( $char != $htmlEntited ){
                $stringBuilder .= $htmlEntited;
            } elseif( $decValue >= 128 ){
                $stringBuilder .= "&#" . $decValue . ";";
            } else {
                $stringBuilder .= $char;
            }
        }

        return $stringBuilder;
    }

    // source - http://php.net/manual/en/function.ord.php#109812
    public static function ordutf8($string, &$offset) {
        $code = ord(substr($string, $offset,1));
        if ($code >= 128) {        //otherwise 0xxxxxxx
            if ($code < 224) $bytesnumber = 2;                //110xxxxx
            else if ($code < 240) $bytesnumber = 3;        //1110xxxx
            else if ($code < 248) $bytesnumber = 4;    //11110xxx
            $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
            for ($i = 2; $i <= $bytesnumber; $i++) {
                $offset ++;
                $code2 = ord(substr($string, $offset, 1 - 128));        //10xxxxxx
                $codetemp = $codetemp*64 + $code2;
            }
            $code = $codetemp;
        }
        $offset += 1;
        if ($offset >= strlen($string)) $offset = -1;
        return $code;
    }

    // source - http://php.net/manual/en/function.chr.php#88611
    public static function unichr($u) {
        return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    public static function isBase64($data) {
            if (preg_match('%^[a-zA-Z0-9]*={0,2}$%', $data)) {
                return TRUE;
            } else {
                return FALSE;
            }
    }

    public static function switchToForwardSlashes(String $string){
        return str_replace("\\", "/", $string);
    }

    public static function switchToBackSlashes(String $string){
        return str_replace("/", "\\", $string);
    }


}



