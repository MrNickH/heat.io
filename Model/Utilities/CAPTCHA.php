<?php

namespace Model\Utilities;

class CAPTCHA
{

    public static function checkCaptcha(array $postData)
    {
        if (!isset($postData['g-recaptcha-response']) || empty($postData['g-recaptcha-response'])) {
            return false;
        }

        Network::networkCall('https://www.google.com/recaptcha/api/siteverify', 'POST', [], [
            'secret' => RECAPTCHA_SECRET,
            'response' => $postData['g-recaptcha-response'],
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ], 'application/x-www-form-urlencoded', false);

        return Network::$responseCode == 200;

    }

    public static function getFormSnippet()
    {
        return '<div class="g-recaptcha" data-sitekey="' . RECAPTCHA_KEY . '"></div>';
    }
}