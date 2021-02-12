<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick
 * Date: 29/01/15
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */

class SiteSetting
{
    public static function get($settingName)
    {

        if ($settingName == "homeDomain" && defined('MAINURL')) {
            return MAINURL;
        }

        $wh['name'] = $settingName;
        $result = CRUD::one(TABLEPREFIX . '_system_globalconfig', $wh);
        if (isset($result['settingValue'])) {
            return $result['settingValue'];
        } else {
            throw new \Model\General\GamblersDenException('Invalid Site setting',
                'Who tried to use this site setting?' . $settingName, "Site Setting");
        }
    }

    public static function set($settingName,$value)
    {
        $wh['name'] = $settingName;
        $result = CRUD::update(TABLEPREFIX . '_system_globalconfig', ['settingValue' => $value, 'modified_date' => date('Y-m-d H:i:s')],['name' => $settingName]);
        return true;
    }
}