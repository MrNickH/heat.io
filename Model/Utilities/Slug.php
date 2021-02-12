<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 01/02/19
 * Time: 09:51
 */

namespace Model\Utilities;


class Slug
{
    public static function generateSlug(String $title)
    {
        $lowerCased = strtolower($title);
        $spacesToDashes = str_replace(" ","-", $lowerCased);
        $multidashesToOneDash = preg_replace('/-+/', '-', $spacesToDashes);
        $specialsRemoved = preg_replace('/[^a-z0-9\-]/', '', $multidashesToOneDash);
        $maxLength = substr($specialsRemoved,0,50);

        if(strlen($maxLength) < 2){
            return false;
        }

        return $maxLength;

    }
}