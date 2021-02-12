<?php


namespace Model\Utilities;


class DomainUtilities
{
 public static function forceHTTPS()
 {
     if(\StringFunctions::startsWith($_SERVER['REQUEST_URI'],"/index.php")){
         self::directToMAINURL(false);
     }

     if(MAINURL != $_SERVER['HTTP_HOST']){
         self::directToMAINURL();
     }

     if(FORCESECURE){
         if(isset($_SERVER['HTTPS'])){
             if($_SERVER['HTTPS'] != 'on') {
                   self::directToHTTPS();
             }

         } else {
             self::directToHTTPS();
         }
     }
 }


    private static function directToHTTPS()
    {
        header('Location: https://'.MAINURL.$_SERVER['REQUEST_URI'], true, 301);
        die();
    }

    private static function directToMAINURL($rq = true)
    {
        header('Location: '.MAINPROTOCOL.'://'.MAINURL.($rq?$_SERVER['REQUEST_URI']:""), true, 301);
        die();
    }
}