<?php


namespace Model\Utilities;

use PiPHP\GPIO\GPIO as GPIOLib;

class GPIO
{
    private static $lib;

    private static function getGPIOLib()
    {
        return self::$lib ??= new GPIOLib();
    }

    public static function readGPIO(int $pinNumber)
    {
           $lib = self::getGPIOLib();
           $lib->getOutputPin($pinNumber)->getValue();
    }

    public static function setGPIO(int $pinNumber, bool $value)
    {
        $lib = self::getGPIOLib();
        $lib->getOutputPin($pinNumber)->setValue((int) $value);
    }
}