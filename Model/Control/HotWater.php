<?php


namespace Model\Control;


use Model\Utilities\GPIO;

class HotWater
{
    public static function enableHotWater()
    {
        GPIO::setGPIO(HOTWATER_PIN, true);
    }

    public static function disableHotWater()
    {
        GPIO::setGPIO(HOTWATER_PIN, false);
    }

    public static function status()
    {
        return (bool) GPIO::readGPIO(HOTWATER_PIN);
    }
}