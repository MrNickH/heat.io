<?php


namespace Controller;


class settings
{
    public function view_main()
    {
        $pageData = [
            'title' => 'General Settings'
        ];

        \Model\Control\Heating::enableHeating();

        return $pageData;
    }
}