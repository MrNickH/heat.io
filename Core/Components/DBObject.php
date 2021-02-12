<?php

namespace Core\Components;

use Model\General\GamblersDenException;

class DBObject extends \stdClass implements \JsonSerializable
{
    private $table_name;
    private $joint;
    protected $vars;
    private static $tscache;
    private static $tccache;
    private static $objectCache;
    public $complete = false;

    public function __construct(String $table, Array $joint = [], $dumb = false)
    {
        self::tableSizes();
        self::tableColumns();
        $this->table_name = $table;
        $this->joint = $joint;
        $this->builder([], false, $dumb);
    }

    protected function builder(Array $joint = [], $forceFetch = false, $forceNoFetch = false)
    {

        self::tableSizes();
        self::tableColumns();

        $this->joint = sizeof($joint) ? $joint : $this->joint;

        //If the JOINT ARRAY is the same size as the table, then just use the whole thing.
        if ((sizeof($this->joint) == DBObject::$tscache->{$this->table_name} && !$forceFetch) || $forceNoFetch) {
            $A_Array = $this->joint;
            $key = key($this->joint);
            $this->joint = [$key => $this->joint[key($this->joint)]];
        //Otherwise do a query
        } else {
            if(isset(self::$objectCache[$this->table_name][md5(serialize($this->joint))])){
                $A_Array = self::$objectCache[$this->table_name][md5(serialize($this->joint))];
            } else {
                $A_Array = \CRUD::one($this->table_name, $this->joint);
                self::$objectCache[$this->table_name][md5(serialize($this->joint))] = $A_Array;
            }
        }

        if (!$A_Array) {
            return;
        }

        foreach ($A_Array as $property_name => $value) {
            $this->complete = true;
            $this->vars[$property_name] = $value;
        }
    }

    private static function tableSizes($force = false){
        if(!self::$tscache && !$force){
            if(!($cache = json_decode(\Cache::checkCache('tscache')))){
                $cache = self::build_ts_cache();
                \Cache::putCache('tscache', json_encode($cache));
            }
            self::$tscache = json_decode(json_encode($cache));
        }

        return self::$tscache;
    }

    private static function tableColumns($force = false){
        if(!self::$tccache && !$force){
            if(!($cache = json_decode(\Cache::checkCache('tccache')))){
                $cache = self::build_tc_cache();
                \Cache::putCache('tccache', json_encode($cache));
            }
            self::$tccache = json_decode(json_encode($cache));
        }

        return self::$tccache;
    }

    private static function build_ts_cache()
    {
        $tables = \CRUD::manualQuery('show tables;');
        $return_tables = [];
        foreach($tables as $table){
            $row = \CRUD::one($table['Tables_in_'.DBNAME],[1=>1],null,'ASC');
            $return_tables[$table['Tables_in_'.DBNAME]] = sizeof($row);
        }
        return $return_tables;
    }

    public static function build_tc_cache()
    {
        $tables = \CRUD::manualQuery('show tables;');
        $return_tables = [];
        foreach($tables as $table){
            $rows = \CRUD::manualQuery('SHOW FIELDS FROM '.$table['Tables_in_'.DBNAME]);
            foreach($rows as $row){
                $return_tables[$table['Tables_in_'.DBNAME]][$row['Field']] = $row;
            }
        }
        return $return_tables;
    }

    public static function quick_property($name, $joint, $table = "")
    {
        $data = \CRUD::one($table, $joint);
        return $data[$name];
    }

    public function remove(bool $sure = false)
    {
        $result = $sure ? \CRUD::delete($this->table_name, $this->joint, 1) : false;
        return $result;
    }

    public function __get(String $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (array_key_exists($property, $this->vars)) {
            return $this->vars[$property];
        }

        $this->builder([], true);

        if (property_exists($this, $property)) {
            return $this->$property;
        }

        if (isset($this->vars[$property])) {
            return $this->vars[$property];
        }

        return false;
    }

    public function __set(String $property, $value)
    {
        if (property_exists($this, $property)) {
            $this->db_update($property, $value);
            $this->$property = $value;
            return true;
        }

        if (key_exists($property, $this->vars)) {
            $this->db_update($property, $value);
            $this->vars[$property] = $value;
            return true;
        }

        //Transient only.
        $this->vars[$property] = $value;
    }

    private function db_update(String $property, $value)
    {
        $array = [
          $property => $value,
        ];

        $tc = self::tableColumns();

        if(self::tableColumns()->{$this->table_name}->modified_date && $property != "modified_date"){
            $array["modified_date"] = date("Y-m-d H:i:s");
        }

        \CRUD::update($this->table_name, [$property => $value], $this->joint);
    }

    public function create_new(Array $row_data, $table_name = null)
    {
        self::tableSizes();
        self::tableColumns();

        $table_name = $table_name ?? $this->table_name;

        if ($table_name) {
            $addResult = \CRUD::create($table_name, $row_data);
            $tc = current(self::$tccache->$table_name)->Field;
            if ($addResult && $tc) {
                return [$tc => $addResult];
            }

            self::tableSizes(true);
            self::tableColumns(true);

            if ($addResult) {
                $tc = current(self::$tccache->$table_name)->Field;
                return [$tc => $addResult];
            }

            throw new Exception();
        }
    }

    public function jsonSerialize()
    {
        return $this->vars;
    }

    public function count()
    {
        return \CRUD::numRows(self::table);
    }
}