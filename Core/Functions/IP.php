<?php

class IP
{

    private $address;
    private $hits;
    private $lastUpdated;
    private $firstAccessed;
    private $IP;

    public function __construct($IP)
    {
        $retArr['IP'] = $IP;
        $data = CRUD::one(TABLEPREFIX . "_iprestrict", $retArr);

        if ($data) {
            $method = "existing";
            $data = $data['IP'];
        } else {
            $method = "new";
            $data = $IP;
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
                    "The constructor argument 1 for IP was not valid", "Core Error");
                break;
        }

        $this->checkForDecrements();

    }

    private function builder($IP)
    {
        $retArr['IP'] = $IP;
        $data = CRUD::one(TABLEPREFIX . "_iprestrict", $retArr);
        if (isset($data)) {
            $this->address = $data['IP'];
            $this->hits = $data['hits'];
            $this->lastUpdated = $data['lastupdate'];
            $this->firstAccessed = $data['create'];
        } else {
            throw new \Model\General\GamblersDenException("IP Address Not created.",
                "You have tried to create an IP object without actually creating the IP in the DB.", "Core Error");
        }
    }

    private function inserter($IP)
    {
        $insertArray['IP'] = $IP;
        $insertArray['create'] = date("Y-m-d H:i:s");
        CRUD::create(TABLEPREFIX . "_iprestrict", $insertArray);
    }

    private function checkForDecrements()
    {
        if ($this->hits != 0) {
            $secondsForUpdate = SiteSetting::get('spamsecSCORE-cooldown');
            $secondsDifference = (time() - strtotime($this->lastUpdated));
            $numberToReduceHitsBy = floor($secondsDifference / $secondsForUpdate);

            if ($this->hits < $numberToReduceHitsBy) {
                $nhv = '0';
            } else {
                $nhv = $this->hits - $numberToReduceHitsBy;
            }

            $whereArray['IP'] = $this->address;
            $updArray['hits'] = $nhv;

            CRUD::update(TABLEPREFIX . "_iprestrict", $updArray, $whereArray);

            $this->hits = $updArray['Hits'];
            $this->lastUpdated = date("D-m-y H:i:s");
        }
    }

    public function checkIP()
    {
        if ($this->hits < SiteSetting::get('spamsecSCORE-capt')) {
            return "OK";
        } elseif ($this->hits < SiteSetting::get('spamsecSCORE-block')) {
            return "VALIDATE";
        } else {
            return "BANNED";
        }

    }

    public function adjustIP($amount)
    {

        $whereArray['IP'] = $this->address;
        $updArray['hits'] = $this->hits += $amount;

        CRUD::update(TABLEPREFIX . "_iprestrict", $updArray, $whereArray);

        $this->hits = $updArray['hits'];
        $this->lastUpdated = date("D-m-y H:i:s");
    }

    public function banIP()
    {

    }
}