<?php

use Model\Accounts\User;
use Model\General\Log;
use Aws\DynamoDb\SessionHandler;
use Aws\DynamoDb\DynamoDbClient;

class SiteSession
{


    public static $skipCheck = false;

    private static function setupDynamoDB()
    {

        $dynamoDb = DynamoDbClient::factory([
            'region' => 'eu-west-2' ,
            'version' => '2012-08-10',
            'credentials' => [
                'key' => AWS_KEY,
                'secret' => AWS_SECRET
            ]
        ]);
        $sessionHandler = $dynamoDb->registerSessionHandler([
            'table_name' => AWS_SESSION_TABLE
        ]);
    }


    public static function checkgenSession()
    {
        if (self::$skipCheck) {
            return;
        }

        self::sessionReStart();

        if (self::getSessionVar('fingerprint') == null) {
            $newFingerPrint = self::generateUniqueFingerPrint();

            self::setSessionVar('fingerprint', $newFingerPrint);

            $cacheCheck = Cache::checkCache('rate-' . $newFingerPrint);
            if ($cacheCheck) {
                Cache::deleteCache('rate-' . $newFingerPrint);
                self::setSessionVar('actionCounter', (array)json_decode($cacheCheck));

            }

        }

        $currentFingerPrint = self::generateUniqueFingerPrint();
        $storedFingerPrint = self::getSessionVar('fingerprint');

        if ($storedFingerPrint != $currentFingerPrint){
            self::sessionDestroy();
        }


        self::performAction();
        Cache::putCache('rate-' . $storedFingerPrint, json_encode(self::getActionCounter()));

        if (self::checkEventCount()) {
            if (self::countEvents() <= RATE_LIMIT_LOG_LIMIT) {
                header('Location: /error/429', true);
            }
            Log::LogData('Security', 'Rate limit exceeded -> Events in the last ' . RATE_LIMIT_WINDOW . ' seconds is ' . self::countEvents());
            header('Location: /error/429', true);
        }

    }

    public static function sessionReStart()
    {
        //self::setupDynamoDB();
        session_name(SESSIONNAME);
        session_start();

        //self::setSessionVar('TIME', time());

        /*$oldId = self::getSessionVar('OLDID');

        //GENERATE NEW SESSION ID
        if ($oldId) {
            $sessdata = $_SESSION;
            session_id($oldId);
            session_destroy();
            session_start();
            $_SESSION = $sessdata;
        }

        $time = self::getSessionVar('TIME');

        //IF NO TIME STORED OR THE SESSION OLDER THAN 30 SECONDS - DO A REGEN NEXT TIME
        if (!$time || $time + LOGIN_TIMEOUT < time()) {
            self::setSessionVar('OLDID', session_id());
            self::setSessionVar('TIME', time());
            session_regenerate_id();
        } else {
            self::unsetSessionVar('OLDID');
        }*/
    }

    public static function getSessionVar(String $name)
    {
        if (isset($_SESSION[$name]) && $_SESSION[$name]) {
            return $_SESSION[$name];
        }
        return null;
    }

    public static function setSessionVar(String $name, $value = null)
    {
        if ($value) {
            $_SESSION[$name] = $value;
            return true;
        }
        return false;
    }

    public static function unsetSessionVar(String $name)
    {
        $_SESSION[$name] = null;
        return true;
    }

    public static function generateUniqueFingerPrint($user_id_override = 0)
    {
        if($user_id_override){
            $uid = $user_id_override;
        }else if(self::LOU()){
            $uid = self::LOU()->user_id;
        }else
        {
            $uid = 0;
        }


        $s['userID'] = $uid;
        $s['UA'] = $_SERVER['HTTP_USER_AGENT'];
        $s['RA'] = $_SERVER['REMOTE_ADDR'];
        $s['RP'] = $_SERVER['RP'];

        return md5(implode("~s~", $s));

    }

    public static function LOU()
    {
        $user = self::getSessionVar('user');

        if ($user && $user instanceof User && $user->isLoggedIn()) {
            /* @var $user User */
            return $user;
        }

        return null;

    }

    public static function sessionDestroy()
    {
        session_destroy();
    }

    public static function performAction()
    {
        $actionCounter = self::getActionCounter();
        $actionCounter[date("YmdHi")]++;
        self::setSessionVar('actionCounter', $actionCounter);
    }

    private static function getActionCounter()
    {
        if (!self::getSessionVar("actionCounter")) {
            self::setSessionVar("actionCounter", [date("YmdHi") => 0]);
        }

        return self::getSessionVar("actionCounter");
    }

    private static function checkEventCount()
    {
        self::clearOldEvents();
        $recentEvents = self::countEvents();
        if ($recentEvents > RATE_LIMIT_THRESHOLD) {

            return true;
        }

        return false;
    }

    private static function clearOldEvents()
    {
        $actionCounter = self::getActionCounter();
        foreach ($actionCounter as $time => $actions) {
            if (time() - RATE_LIMIT_WINDOW > $time) {
                unset($actionCounter[$time]);
            }
        }
        self::setSessionVar('actionCounter', $actionCounter);

    }

    private static function countEvents()
    {
        return 0;
        $actionCounter = self::getActionCounter();
        return array_sum($actionCounter);
    }

    public static function checkSession()
    {
        if (self::getSessionVar('TIME')) {
            return true;
        }
    }

    public static function impersonateLOU($user)
    {
        if(!self::LOU()){
            return false;
        }

        $userID = self::LOU()->user_id;

        self::setSessionVar('oldUser', self::getSessionVar('user'));
        User::doLogin($user->user_id, "Impersonation by ".$userID);
    }

    public static function unimpersonateLOU()
    {
        if(!self::LOU()){
            return false;
        }

        if(!self::getSessionVar('oldUser')){
            return false;
        }

        self::LOU()->logout("Impersontation Ended");

        self::setSessionVar('user', self::getSessionVar('oldUser'));
        self::unsetSessionVar('oldUser');

        self::setSessionVar('fingerprint', self::generateUniqueFingerPrint());
    }

    public static function setLOU($user)
    {

        if ($user instanceof User && $user->isLoggedIn()) {
            self::unsetSessionVar('fingerprint');
            self::setSessionVar('user', $user);
            return true;
        }
        return false;
    }

    public static function removeLOU()
    {
        self::unsetSessionVar('user');
        self::unsetSessionVar('fingerprint');
        self::setSessionVar('fingerprint', self::generateUniqueFingerPrint(0));
    }
}