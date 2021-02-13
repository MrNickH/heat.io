<?php


namespace Model\Utilities;

use PhpGpio\Gpio as GPIOLib;

class GPIO
{
    private static $lib;

    private static function getGPIOLib()
    {
        if (!isset(self::$lib)) {
            self::$lib = new GPIOLib();
        }
        return self::$lib;
    }

    public static function readGPIO(int $pinNumber)
    {
        return self::readValuePin($pinNumber);
    }

    /**
     * Read pin value.
     *
     * @param int, $pinNo
     * @return bool|string
     */
    public static function readValuePin($pinNo) {
        $file = '/sys/class/gpio/gpio'.$pinNo.'/value';
        if(!file_exists($file)) {
            return false;
        }

        return trim(file_get_contents('/sys/class/gpio/gpio'.$pinNo.'/value'));
    }

    public static function setGPIO(int $pinNumber, int $value)
    {
        $lib = self::getGPIOLib();
        $lib->setup($pinNumber, 'out');
        $lib->output($pinNumber, (int)$value);
    }
}