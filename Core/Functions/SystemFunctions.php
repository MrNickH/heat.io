<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 12/08/2015
 * Time: 11:44
 */

class SystemFunctions
{

    public static function isSecure()
    {
        return (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443
            || (
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
                || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on')
            )
        );
    }

    public static function getDataBaseUsedSpace()
    {
        $q = 'SELECT table_schema "' . DBNAME . '",sum( data_length + index_length ) / 1024 /1024 "DBSMB", sum( data_free )/ 1024 / 1024 "Free Space in MB" FROM information_schema.TABLES GROUP BY table_schema ;';
        foreach (CRUD::manualQuery($q) as $row) {
            if ($row[DBNAME] == DBNAME) {
                return $row['DBSMB'] . " MB";
            }

        }
        return "Not attainable";
    }
}