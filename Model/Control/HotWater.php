<?php


namespace Model\Control;

use Model\Utilities\GPIO;

class HotWater
{
    public static function enableHotWater()
    {
        GPIO::setGPIO(HOTWATER_PIN, 1);
    }

    public static function disableHotWater()
    {
        GPIO::setGPIO(HOTWATER_PIN, 0);
    }

    public static function status()
    {
        return (bool) GPIO::readGPIO(HOTWATER_PIN);
    }
}