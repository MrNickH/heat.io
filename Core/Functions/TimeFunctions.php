<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 23/07/2015
 * Time: 15:23
 */

class TimeFunctions
{
    public static function convertMinutesToHours($mins)
    {
        $hours = $mins / 60;
        $wholeHours = floor($hours);
        $portion = (($hours - $wholeHours) * 60);

        if ($portion < 10) {
            $portion = "0" . $portion;
        }

        return number_format($wholeHours, 0) . ":" . $portion;

    }

    public static function convertSecondsToHours($seconds)
    {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours * 3600)) / 60);
        $secs = floor($seconds % 60);


        if ($hours < 10) {
            $hours = "0" . $hours;
        }

        if ($mins < 10) {
            $mins = "0" . $mins;
        }

        if ($secs < 10) {
            $secs = "0" . $secs;
        }


        return $hours . ":" . $mins . ":" . $secs;

    }

    public static function convertSecondsToApproximateDuration($seconds)
    {
        $years =   floor($seconds / 60 / 60 / 24 / 30 / 12);
        $months =  floor($seconds / 60 / 60 / 24 / 30);
        $weeks =   floor($seconds / 60 / 60 / 24 / 7);
        $days =    floor($seconds / 60 / 60 / 24);
        $hours =   floor($seconds / 60 / 60 );
        $minutes = floor($seconds / 60 );
        $secs =    floor($seconds);

        if ($years > 1) {
            return  $years . " years";
        } else if ($months > 1) {
            return $months . " months";
        } else if ($weeks > 1) {
            return $weeks . " weeks";
        } else if ($days > 1) {
            return $days . " days";
        } else if ($hours > 1) {
            return $hours . " hours";
        } else if ($minutes > 1) {
            return $minutes . " minutes";
        } else {
            return $secs . " seconds";
        }
    }

    public static function convertSecondsToScalingDate($seconds)
    {
        $years =   floor($seconds / 60 / 60 / 24 / 30 / 12);
        $months =  floor($seconds / 60 / 60 / 24 / 30);
        $weeks =   floor($seconds / 60 / 60 / 24 / 7);
        $days =    floor($seconds / 60 / 60 / 24);
        $hours =   floor($seconds / 60 / 60 );
        $minutes = floor($seconds / 60 );
        $secs =    floor($seconds);

        if ($years >= 1) {
            return  date('M Y', time() - $seconds);
        } else if ($months > 1) {
            return  date('M', time() - $seconds);
        } else if ($weeks >= 1) {
            return $weeks . " week".($weeks>1?'s':'');
        } else if ($days > 1) {
            return date('D', time() - $seconds);
        } else if ($days == 1) {
            return 'Yesterday';
        } else if ($hours >= 12) {
            return $hours.'h';
        } else if ($hours >= 1) {
            return date('g:ia', time() - $seconds);
        } else if ($minutes > 1) {
            return $minutes."m";
        } else {
            return 'Just now';
        }
    }
}
