<?php
/**
 * Created by PhpStorm.
 * User: Nick
 * Date: 16/02/2015
 * Time: 11:22
 */

class UserLockout
{
    private $UserID;
    private $hits;
    private $overAllHits;
    private $lastUpdated;

    public function __construct($UserID)
    {
        $retArr['user'] = $UserID;
        $data = CRUD::one("sy_userlockout", $retArr);

        if ($data) {
            $method = "existing";
            $data = $data['UserID'];
        } else {
            $method = "new";
            $data = $UserID;
        }


        switch ($method) {
            case "existing":
                $this->builder($data);
                break;

            case "new":
                $this->inserter($data);  //inserter then retrieve ID
                $this->builder($data);
                break;

            default:
                throw new \Model\General\GamblersDenException("Invalid Constructor",
                    "The constructor argument 1 for User was not valid", "Core Error");
                break;
        }

        $this->checkForDecrements();

    }

    private function builder($UserID)
    {
        $retArr['user'] = $UserID;
        $data = CRUD::one("sy_userlockout", $retArr);

        if (isset($data)) {
            $this->UserID = $data['UserID'];
            $this->hits = $data['Hits'];
            $this->overAllHits = $data['OverallHits'];
            $this->lastUpdated = $data['LastUpdated'];
        } else {
            throw new \Model\General\GamblersDenException("IP Address Not created.",
                "You have tried to create an IP object without actually creating the IP in the DB.", "Core Error");
        }
    }

    private function inserter($UserID)
    {
        $insertArray['user'] = $UserID;
        $insertArray['hits'] = 0;
        $insertArray['totalhits'] = 0;
        CRUD::create("sy_userlockout", $insertArray);
    }

    private function checkForDecrements()
    {
        if ($this->hits != 0) {
            $minutesForUpdate = SiteSetting::get('spamsecLOGIN-triesTimeWindow');
            $minutesDifference = (time() - strtotime($this->lastUpdated)) / 60;
            $numberToReduceHitsBy = floor($minutesDifference / $minutesForUpdate);

            if ($this->hits < $numberToReduceHitsBy) {
                $nhv = '0';
            } else {
                $nhv = $this->hits - $numberToReduceHitsBy;
            }

            $whereArray['user'] = $this->UserID;
            $updArray['hits'] = $nhv;

            CRUD::update("sy_userlockout", $updArray, $whereArray);

            $this->hits = $updArray['Hits'];
            $this->lastUpdated = date("D-m-y H:i:s");
        }

        if ($this->overAllHits >= SiteSetting::get('spamsecLOGIN-triesToLockoutO')) {
            if ((time() - strtotime($this->lastUpdated)) >= (SiteSetting::get('spamsecLOGIN-OlockoutWindow') * 60)) {
                $this->overAllHits = 0;

                $whereArray['user'] = $this->UserID;
                $updArray['totalhits'] = 0;

                CRUD::update("sy_userlockout", $updArray, $whereArray);
            }
        }
    }

    public function checkUser()
    {

        if ($this->hits >= SiteSetting::get('spamsecLOGIN-triesToLockoutT')) {
            return false;
        }

        if ($this->overAllHits >= SiteSetting::get('spamsecLOGIN-triesToLockoutO')) {
            return false;
        }

        return true;
    }

    public function incrementAttempts()
    {
        $whereArray['user'] = $this->UserID;
        $updArray['totalhits'] = $this->overAllHits + 1;
        $updArray['hits'] = $this->hits + 1;

        CRUD::update("sy_userlockout", $updArray, $whereArray);
    }


}