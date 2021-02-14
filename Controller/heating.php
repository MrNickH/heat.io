<?php


namespace Controller;


class heating
{
    public function view_main()
    {
        $pageData = [
            'title' => 'Heating',
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
        \Model\Control\Heating::enableHeating();
    }


    public function view_off()
    {
        \Model\Control\Heating::disableHeating();
    }
}