<?php

namespace Model\Utilities;

class EmailQueue extends \Core\Components\DBObject
{
    protected const table = TABLEPREFIX.'_email_queue';

    public function __construct($data, $new = false)
    {

        if ($new) {
            $data = parent::create_new($data, self::table);
        }

        parent::__construct(self::table, $data);
    }

}