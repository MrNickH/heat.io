<?php


namespace Model\Utilities;

use PhpGpio\Gpio as GPIOLib;

class GPIO
{
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

        return (bool) !(trim(file_get_contents('/sys/class/gpio/gpio'.$pinNo.'/value')));
    }

    public static function setGPIO(int $pinNumber, bool $value)
    {
        if (!file_exists('/sys/class/gpio/gpio' . $pinNumber . '/direction')) {
            // Export pin
            file_put_contents('/sys/class/gpio/export', $pinNumber);
            file_put_contents('/sys/class/gpio/gpio'.$pinNumber.'/direction', 'out');
        }

        file_put_contents('/sys/class/gpio/gpio'.$pinNumber.'/value', (int)(!$value));
    }
}