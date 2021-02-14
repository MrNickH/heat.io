<?php


namespace Controller;


class heating
{

    public function view_main(){}

    public function view_settings(){}

    public function view_schedule(){}

    public function view_boost()
    {
        \Model\Control\Heating::enableHeating();
        return [
            'redirect' => '/heating/schedule'
        ];
    }


    public function view_on()
    {
        \Model\Control\Heating::enableHeating();
        return [
            'redirect' => '/heating'
        ];
    }

    public function view_off()
    {
        \Model\Control\Heating::disableHeating();
        return [
            'redirect' => '/heating'
        ];
    }

}