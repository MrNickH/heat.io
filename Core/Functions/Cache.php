<?php

class Cache
{
    public static function checkCache(String $hash)
    {
        $fileName = 'Cache/' . $hash;

        //Check for file
        if (!file_exists($fileName)) {
            return false;
        }

        //Check for validity
        $contentsOfFile = json_decode(file_get_contents($fileName));
        if (!$contentsOfFile) {
            unlink($fileName);
            return false;
        }

        //Check for expiry
        if ($contentsOfFile->expiry < time()) {
            unlink($fileName);
            return false;
        }

        $data =  base64_decode($contentsOfFile->data);

        switch($contentsOfFile->type){
            case "object":
                $data = unserialize($data);
                break;
            case "array":
                $data = json_decode($data);
                break;
        }

        return $data;

    }

    public static function putCache(String $hash, $data, int $expiry = 6000)
    {
        $fileName = 'Cache/' . $hash;

        $type = "string";

        if(is_object($data)){
            $type = "object";
            $data = serialize($data);
        }

        if(is_array($data)){
            $type = "array";
            $data = json_encode($data);
        }

        $jsonData = [
            'expiry' => time() + $expiry,
            'data' => base64_encode($data),
            'type' => $type
        ];

       file_put_contents($fileName, json_encode($jsonData));
    }

    public static function deleteCache(String $hash)
    {
        $fileName = 'Cache/' . $hash;
        unlink($fileName);
    }

    public static function purgeOld()
    {
        if ($handle = opendir('Cache')) {
            while (false !== ($file = readdir($handle))) {
                if($file == "." || $file == ".." || $file == ".htaccess"){
                    continue;
                }
                $file = 'Cache/'.$file;
                $fileLastModified = filectime($file);
                if (filectime($file)< (time()-86400*30)) {  // 86400 = 60*60*24
                    unlink($file);
                }
            }
        }
    }
}