<?php

class Password
{

    static public function checkPassword($passwordPlaintext, $passwordHash)
    {

        $saltArray = str_split($passwordHash, 4); //NEED TO GET SALT

        foreach ($saltArray as $saltBlock) {
            $salt[] = substr($saltBlock, -1);   //LAST CHARACTER IN EACH BLOCK IS A CHARACTER IN THE PASSWORD SALT
        }

        $wholePass = Password::generatePassword($passwordPlaintext, implode("", $salt));

        if ($wholePass === $passwordHash) {
            return true;
        } else {
            return false;
        }

    }

    public static function generatePassword($plain, $salt = null)
    {

        if ($salt) {
            $randSalt = $salt;
        } else {
            $randSalt = md5(date("Y-m-d H:i:s") . rand(1, 1000));
        }

        $nonEncrypt = $randSalt . $plain . $randSalt;
        $encrypt = hash("sha384", $nonEncrypt);

        $saltSplit = str_split($randSalt);
        $encryptSplit = str_split($encrypt, 3);

        foreach ($encryptSplit as $key => $third) {
            $finalArray[] = $third;
            $finalArray[] = $saltSplit[$key];
        }

        return $finalPass = implode("", $finalArray);

    }

    public static function isSecureEnough(String $password)
    {
        //Length check
        if (strlen($password) <= 7) {
            return false;
        }

        $typeCheck = 0;
        //3/4 Types
        if (preg_match("#[0-9]+#", $password)) {
            $typeCheck++;
        }

        if (preg_match("#[a-z]+#", $password)) {
            $typeCheck++;
        }

        if (preg_match("#[A-Z]+#", $password)) {
            $typeCheck++;
        }

        if (preg_match("/[$-/:-?{-~!\"^_`\[\]]/", $password)) {
            $typeCheck++;
        }

        if ($typeCheck <= 1) {
            return false;
        }

        return true;

    }
}