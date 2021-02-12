<?php

class Cachebuster
{
    private static $cacheBustedHashes;

    public static function getHash($fileName)
    {
        if (!Cachebuster::$cacheBustedHashes) {
            Cachebuster::$cacheBustedHashes = include_once('Core/cachebuster.php');
        }

        return Cachebuster::$cacheBustedHashes[$fileName];
    }
}