<?php


namespace Model\Control;


use Model\Utilities\GPIO;

class Heating
{
    public static function enableHeating()
    {
        GPIO::setGPIO(HEATING_PIN, true);
    }

    public static function disableHeating()
    {
        GPIO::setGPIO(HEATING_PIN, false);
    }

    public static function status()
    {
        return (bool) GPIO::readGPIO(HEATING_PIN);
    }


}