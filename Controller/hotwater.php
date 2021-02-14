<?php


namespace Controller;


class hotwater
{

    public function view_main(){}

    public function view_settings(){}

    public function view_schedule(){}

    public function view_boost()
    {
        \Model\Control\HotWater::enableHotWater();
        return [
            'redirect' => '/hotwater/schedule'
        ];
    }


    public function view_on()
    {
        \Model\Control\HotWater::enableHotWater();
        return [
            'redirect' => '/hotwater'
        ];
    }

    public function view_off()
    {
        \Model\Control\HotWater::disableHotWater();
        return [
            'redirect' => '/hotwater'
        ];
    }

}