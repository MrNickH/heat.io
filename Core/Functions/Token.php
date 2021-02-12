<?php

class Token
{

    /*private $I_ID;
    private $D_dateTime;
    private $S_token;
    private $S_salt;

    public function __construct($method, $data){

        switch($method){
            case "generate":
                $ID = $this->inserter($data);  //inserter then retrieve ID
                $this->builder($ID);
                break;

            case "existing":
                $this->retrieveToken();
                $this->builder($data);
                break;

            default:
                return false;
                break;
        }
    }*/

    /*private function builder($I_ID){
        $values['ID'] = $I_ID;
        $A_tokenArray = CRUD::retrieve("sa_token",$values, true);

        $this->I_ID = $A_tokenArray['ID'];
        $this->D_dateTime = $A_tokenArray['dateTime'];
        $this->S_token = $A_tokenArray['token'];
        $this->S_salt = $A_tokenArray['salt'];

        print_r($A_tokenArray);
    }

    private function inserter($data){
        CRUD::create("sa_token", $data);
    }

    private function retrieveToken(){

    }*/


    public static function validateToken($action, $string)
    {

        $decToc = Token::decrpytToken($string);
        $tokenDB = CRUD::one("sy_token", $decToc);

        if (sizeof($tokenDB) == 0) {
            return false;
        }

        //MAKE NEW TOKEN WITH THE TIME NOW TO THE NEAREST HOUR
        $currentDateTime = date("Y-m-d H:00:00");

        $userID = SiteSession::LOU()->getIID();

        $appendedPlainToken = $currentDateTime . $action . $decToc['salt'] . $userID;
        $token = hash("sha512", $appendedPlainToken);

        if ($tokenDB['token'] === $token) {
            return true;
        }

        //Try again an hour back!

        $currentDateTime = date("Y-m-d H:00:00", time() - 3600);

        $appendedPlainToken = $currentDateTime . $action . $decToc['salt'] . $userID;
        $token = hash("sha512", $appendedPlainToken);

        if ($tokenDB['token'] === $token) {
            return true;
        }

        return false;


    }

    public static function decrpytToken($token)
    {
        $key = "qeWFMkR76zfNr";

        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($token), MCRYPT_MODE_CBC,
            md5(md5($key))), "\0");

        $strLen = strlen($decrypted);       //CALCULATE LENGTH OF DECRYPTED STRING
        $remaining = ($strLen - 128);       //TAKE OFF TOKEN TO FIND id AND salt
        $num = $remaining - 10;             //ID length (10 TAKEN AWAY [SALT])

        $token = substr($decrypted, 0, 128);
        $ID = substr($decrypted, 128, $num);
        $randSalt = substr($decrypted, -10);

        $values['token'] = $token;
        $values['ID'] = $ID;
        $values['salt'] = $randSalt;

        return $values;
    }

    public static function generateToken($action)
    {

        $userID = SiteSession::LOU()->getIID();
        $currDate = date("Y-m-d H:00:00");

        $longSalt = md5(md5($currDate) . rand(1, 1000));
        $randSalt = substr($longSalt, 0, -22);      //10 CHARS LONG

        $appendedPlainToken = $currDate . $action . $randSalt . $userID;

        $token = hash("sha512", $appendedPlainToken);

        $values['dateTime'] = $currDate;
        $values['token'] = $token;
        $values['salt'] = $randSalt;
        $values['action'] = $action;

        $ID = CRUD::create("sy_token", $values);

        $key = "qeWFMkR76zfNr";
        $string = $token . $ID . $randSalt;

        return $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC,
            md5(md5($key))));

    }

    public static function destroyToken($token)
    {
        $decToc = Token::decrpytToken($token);
        CRUD::delete("sy_token", $decToc);
    }

}