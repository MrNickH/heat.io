<?php


namespace Model\Utilities;

class EmailTracker extends \Core\Components\DBObject
{
    protected const table = TABLEPREFIX.'_email_tracking';

    public function __construct($data, $new = false)
    {
        if ($new) {

            if(!isset($data['tracking_code'])){
                $data['tracking_code'] = self::generateTrackingID();
            }

            if(!isset($data['type'])){
                if(!isset($data['outbound'])) {
                    $data['type'] = 0;
                } else {
                    $data['type'] = 1;
                }
            }

            $data = parent::create_new($data, self::table);
        }

        parent::__construct(self::table, $data);
    }

    public static function generateTrackingID(){
        return str_replace ("=","",
            str_replace ("+","",
                str_replace ("/","",
                    base64_encode(rand(0,10000).microtime().random_bytes(20).time().rand(0,10000))
                )
            )
        );
    }

    public function getCode(){
        if($this->type == 0){
            return '<img src="'.MAINPROTOCOL.'://'.MAINURL.'/track/pixel/'.$this->tracking_code.'" width=1 height=1  alt=""/>';
        } else if($this->type == 1) {
            return MAINPROTOCOL.'://'.MAINURL.'/track/link/'.$this->tracking_code;
        } else {
            return MAINPROTOCOL.'://'.MAINURL.'/track/unsubscribe/'.$this->tracking_code;
        }
    }
}