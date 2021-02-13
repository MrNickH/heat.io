<?php

namespace Controller;

use Model\Accounts\User;
use Model\Casino\Casino;
use Model\Media\Streamer;
use \CRUD;

class home implements \Abstracts\controller
{

    public function view_main()
    {
        $pageData = [
            'title' => 'Home',
        ];

        return $pageData;
    }

    public static function view_all(array $pageData)
    {
        $pageData['heatingStatus']  =\Model\Control\Heating::status();
        $pageData['hwStatus'] =  \Model\Control\HotWater::status();
        return $pageData;
    }
}