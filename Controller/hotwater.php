<?php


namespace Controller;


class hotwater
{

    public function view_main()
    {
        $pageData = [
            'title' => 'Hot Water'
        ];

        return $pageData;
    }

    public function view_settings()
    {
        $pageData = [
            'title' => 'Hot Water Settings'
        ];

        return $pageData;
    }



    public function view_on()
    {
        \Model\Control\HotWater::enableHotWater();
    }


    public function view_off()
    {
        \Model\Control\HotWater::disableHotWater();
    }

}