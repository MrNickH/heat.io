<?php


namespace Model\Control;


use Model\Utilities\GPIO;

class Heating
{
    public static function enableHeating()
    {
        GPIO::setGPIO(HEATING_PIN, 1);
    }

    public static function disableHeating()
    {
        GPIO::setGPIO(HEATING_PIN, 0);
    }

    public static function status()
    {
        return (bool) GPIO::readGPIO(HEATING_PIN);
    }


}