<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 07/07/2015
 * Time: 12:28
 */

class DiscoveryFunctions
{
    public static function getOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = array(
            '/windows nt 10/i' => 'Windows',
            '/windows nt 6.3/i' => 'Windows',
            '/windows nt 6.2/i' => 'Windows',
            '/windows nt 6.1/i' => 'Windows',
            '/windows nt 6.0/i' => 'Windows',
            '/windows nt 5.2/i' => 'Windows',
            '/windows nt 5.1/i' => 'Windows',
            '/windows xp/i' => 'Windows',
            '/windows nt 5.0/i' => 'Windows',
            '/windows me/i' => 'Windows',
            '/win98/i' => 'Windows',
            '/win95/i' => 'Windows',
            '/win16/i' => 'Windows',
            '/macintosh|mac os x/i' => 'Mac OS',
            '/mac_powerpc/i' => 'Mac OS',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }

        return $os_platform;
    }

    public static function getOSV()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "?";
        $os_array = array(
            '/windows nt 10/i' => '10',
            '/windows nt 6.3/i' => '8.1',
            '/windows nt 6.2/i' => '8',
            '/windows nt 6.1/i' => '7',
            '/windows nt 6.0/i' => 'Vista',
            '/windows nt 5.2/i' => 'Server 2003/XP x64',
            '/windows nt 5.1/i' => 'XP',
            '/windows xp/i' => 'XP',
            '/windows nt 5.0/i' => '2000',
            '/windows me/i' => 'ME',
            '/win98/i' => '98',
            '/win95/i' => '95',
            '/win16/i' => '3.11',
            '/macintosh|mac os x/i' => 'X+',
            '/mac_powerpc/i' => '9 or less'
        );

        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
                break;
            }
        }

        return $os_platform;
    }

    public static function getBrowser()
    {

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version = "";


        // Next get the name of the useragent yes seperately and for good reason
        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        // check if we have a number
        if ($version == null || $version == "") {
            $version = "";
        }

        if ($bname == "Unknown") {
            return "Drill for UA";
            //return $_SERVER['HTTP_USER_AGENT'];
        }

        return $bname . " (" . $version . ")";

    }

}