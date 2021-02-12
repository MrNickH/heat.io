<?php

class ValidationFunctions
{

    public static function validateTextString($string, $minLength = null, $maxLength = null)
    {

        if (!ctype_alpha($string)) { //STRING IS ALL TEXT
            return false;
        }

        $strLen = strlen($string);

        if (isset($minLength)) {
            //IF ONLY MIN LENGTH SUPPLIED
            if ($strLen < $minLength) {
                return false;
            }
        }

        if (isset($maxLength)) {
            //IF ONLY MAX LENGTH SUPPLIED
            if ($strLen > $maxLength) {
                return false;
            }
        }


        return true;
    }

    public static function validateTitle(String $title, int $max = 50)
    {
        $stringTitleLength = strlen($title);

        if($stringTitleLength < 3 || $stringTitleLength > $max){
            return false;
        }

        $title = strip_tags($title);

        return $title;
    }

    public static function validateNumericString($string, int $minLength = null, int $maxLength = null)
    {

        if (!is_numeric($string)) { //STRING IS ALL NUMBERS
            false;
        }

        $strLen = strlen($string);

        if (isset($minLength)) {
            //IF ONLY MIN LENGTH SUPPLIED
            if ($strLen < $minLength) {
                return false;
            }
        }

        if (isset($maxLength)) {
            //IF ONLY MAX LENGTH SUPPLIED
            if ($strLen > $maxLength) {
                return false;
            }
        }

        return true;
    }

    public static function validateEmailAddress($email)
    {
        $atIndex = strrpos($email, "@");
        if (is_bool($atIndex) && !$atIndex) {
            return false;
        }

        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);

        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            return false;
        }

        if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            return false;
        }

        if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            return false;
        }

        if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            return false;
        }

        if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            return false;
        }

        if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            return false;
        }

        if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
            str_replace("\\\\", "", $local))) {
            // character not valid in local part unless
            // local part is quoted

            return false;
        }


        if (!checkdnsrr($domain, "MX")) {
            // domain not found in DNS
            //IF EMAIL PASSES PREGEX AND DOES NOT HAVE VALID MX RECORD
            return false;
        }

        return true;
    }

    public static function validateUsername($username){
        //====UsernameChecks====
        if (strlen($username) < 6 || strlen($username) > 40) {
            return false;

        }

        if (!preg_match('/^[a-zA-Z0-9-]*$/', $username)) {
            return false;
        }

        return true;

    }

    public static function validateRegex($string, $regex)
    {
        return ( preg_match('/'.$regex.'/', $string, $oa) );
    }

    public static function validateEmailDomain($emailDomain)
    {

        if (!checkdnsrr($emailDomain, "MX")) {
            // domain not found in DNS
            //IF EMAIL PASSES PREGEX AND DOES NOT HAVE VALID MX RECORD
            return false;
        } else {
            return true;
        }

    }

    public static function validateMobileNumber($mobileNum)
    {
        if (is_numeric($mobileNum)) {
            return true;
        }
    }

    public static function emailProviderValidation($email)
    {
        //RETURN TRUE IF EMAIL IS BLACKLISTED...

        $blacklistValues = GlobalConfig::getGlobalSettingValue("validation-emailBlacklist");    //CHECK DOMAIN IS NOT FREE EMAIL PROVIDER
        $blacklistArr = explode(",", $blacklistValues);
        $emailArr = explode("@", $email);


        foreach ($blacklistArr as $blacklistedEmail) {

            if ($emailArr[1] === $blacklistedEmail) {
                //EMAIL IS BLACKLISTED
                return true;
            }

        }
    }

    public static function validateURL($url)
    {
        return preg_match(
            '/^(https?):\/\/'.                                         // protocol
            '(([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+'.         // username
            '(:([a-z0-9$_\.\+!\*\'\(\),;\?&=-]|%[0-9a-f]{2})+)?'.      // password
            '@)?(?#'.                                                  // auth requires @
            ')((([a-z0-9]\.|[a-z0-9][a-z0-9-]*[a-z0-9]\.)*'.                      // domain segments AND
            '[a-z][a-z0-9-]*[a-z0-9]'.                                 // top level domain  OR
            '|((\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])\.){3}'.
            '(\d|[1-9]\d|1\d{2}|2[0-4][0-9]|25[0-5])'.                 // IP address
            ')(:\d+)?'.                                                // port
            ')(((\/+([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)*'. // path
            '(\?([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)'.      // query string
            '?)?)?'.                                                   // path and query string optional
            '(#([a-z0-9$_\.\+!\*\'\(\),;:@&=-]|%[0-9a-f]{2})*)?'.      // fragment
            '$/i',
            $url
        );
    }

    public static function sanitiseInput($data)
    {
        if (is_string($data)) {
            return htmlentities($data, ENT_QUOTES);
        }

        if (is_array($data)) {
            foreach ($data as $k => $d) {
                $data[$k] = self::sanitiseInput($d);
            }
        }
        return $data;
    }

}