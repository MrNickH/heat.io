<?php
/* CREATE RETRIEVE UPDATE DELETE */

class CRUD
{

    private static $printQueries;
    private static $countQueries;
    private static $PreppedStatement;
    public static $connection;
    public static $queryCount = 0;

    public static function setPrintQueries($set)
    {
        self::$printQueries = $set;
    }

    public static function setCountQueries($set)
    {
        self::$countQueries = $set;
    }

    private static function init()
    {
        if (!self::$connection) {
            try {
                self::$connection = new \PDO(
                    "mysql:host=" . DBHOST . ";dbname=" . DBNAME . ";charset=utf8mb4", DBUSERNAME,
                    DBPASSWORD
                );
                self::$connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $ex) {
                die($ex->getMessage());
                throw new \Exception(
                    "Database wont connect for some reason." . $ex->getMessage(),
                    "PDO"
                );
            }
        }
    }

    public static function cleanup()
    {
        if (self::$connection && self::$PreppedStatement) {
            self::$PreppedStatement->closeCursor();
        }
    }

    private static function runQuery($query, $executables = [])
    {
        self::$PreppedStatement = self::$connection->prepare($query);
        self::$PreppedStatement->execute($executables);
    }

    /*
          ,ad8888ba,   88888888ba   88        88  88888888ba,
         d8"'    `"8b  88      "8b  88        88  88      `"8b
        d8'            88      ,8P  88        88  88        `8b
        88             88aaaaaa8P'  88        88  88         88
        88             88""""88'    88        88  88         88
        Y8,            88    `8b    88        88  88         8P
         Y8a.    .a8P  88     `8b   Y8a.    .a8P  88      .a8P
          `"Y8888Y"'   88      `8b   `"Y8888Y"'   88888888Y"'
     */
    public static function create(string $table, array $values, bool $ignore = false)
    {
        self::init();

        $columns = [];
        $finalvalues = [];

        foreach ($values as $key => $val) {
            $columns[] = '`' . $key . '`';

            $placeholderName = ":Nval" . $key;

            if ($val === null) {
                $finalvalues[] = 'NULL';
            } else {
                $finalvalues[] = $placeholderName;
                $insertArray[$placeholderName] = $val;
            }
        }

        $finalQuery =
            'INSERT' . ($ignore ? ' IGNORE' : '') . ' INTO ' .
            $table .
            ' (' . implode(', ', $columns) . ') ' .
            ' VALUES ' .
            ' (' . implode(', ', $finalvalues) . ') ' .
            ';';

        if (self::$printQueries) {
            echo $finalQuery . "<br/>";
            var_dump($insertArray);
        }

        $PreppedStatement = self::$connection->prepare($finalQuery);
        $PreppedStatement->execute($insertArray);

        return self::$connection->lastInsertID();
    }

    public static function retrieve(
        $table,
        $find = [1 => 1],
        $sortBy = null,
        $ASC = "ASC",
        $limit = null,
        $abs = false,
        $columns = null,
        $groupby = null
    ): iterable {
        self::init();

        $where = self::buildWhere($find);
        $order = self::buildOrder($sortBy, $abs, $ASC);
        $group = self::buildGroup($groupby);
        $limit = self::buildLimit($limit);
        $cols = self::buildCols($columns);


        $finalQuery =
            'SELECT ' .
            $cols .
            ' FROM ' .
            $table .
            ' WHERE ' .
            implode(" AND ", $where['str']) .
            $order .
            $group .
            $limit .
            ';';

        if (self::$printQueries) {
            echo $finalQuery . "<br/>";
            var_dump($where['find']);
        }

        if (self::$countQueries) {
            self::$queryCount++;
        }

        self::runQuery($finalQuery, $where['find']);

        self::$PreppedStatement->setFetchMode(PDO::FETCH_ASSOC);

        foreach (self::$PreppedStatement as $row) {
            yield $row;
        }

        self::$PreppedStatement->closeCursor();
    }

    public static function update($table, $update, $find)
    {
        self::init();

        if (!$update || !is_array($update)) {
            throw new \Model\General\GamblersDenException(
                "CRUD:Update called Incorrectly",
                "Variables to be Updated were not Valid (either not present or not an array)"
            );
        }

        if (!$find || !is_array($find)) {
            throw new \Model\General\GamblersDenException(
                "CRUD:Update called Incorrectly",
                "Variables to find were not Valid (either not present or not an array)"
            );
        }

        $where = self::buildWhere($find);
        $what = self::buildUpdate($update);

        $finalQuery =
            'UPDATE ' .
            $table .
            ' SET ' .
            implode(', ', $what['str']) .
            ' WHERE ' .
            implode(' AND ', $where['str']) .
            ';';

        $finalExecutionArray = $what['find'] + $where['find'];

        if (self::$printQueries) {
            echo $finalQuery . "<br/>";
            var_dump($finalExecutionArray);
        }

        if (self::$countQueries) {
            self::$queryCount++;
        }

        $PreppedStatement = self::$connection->prepare($finalQuery);
        $PreppedStatement->execute($finalExecutionArray);

        return true;
    }

    public static function delete($table, array $find, int $limit = 1)
    {
        self::init();

        if (!$find || !is_array($find)) {
            throw new \Model\General\GamblersDenException(
                "CRUD:Delete called Incorrectly",
                "Variables to find were not Valid (either not present or not an array)"
            );
        }

        $where = self::buildWhere($find);
        $limit = self::buildLimit($limit);

        $finalQuery =
            'DELETE FROM ' .
            $table .
            ' WHERE ' .
            implode(' AND ', $where['str']) .
            $limit .
            ';';

        if (self::$printQueries) {
            echo $finalQuery . "<br/>";
            var_dump($where['find']);
        }

        if (self::$countQueries) {
            self::$queryCount++;
        }

        $PreppedStatement = self::$connection->prepare($finalQuery);
        $PreppedStatement->execute($where['find']);

        return $PreppedStatement->rowCount();
    }

    /*
        88        88  88888888888  88           88888888ba   88888888888  88888888ba    ad88888ba
        88        88  88           88           88      "8b  88           88      "8b  d8"     "8b
        88        88  88           88           88      ,8P  88           88      ,8P  Y8,
        88aaaaaaaa88  88aaaaa      88           88aaaaaa8P'  88aaaaa      88aaaaaa8P'  `Y8aaaaa,
        88""""""""88  88"""""      88           88""""""'    88"""""      88""""88'      `"""""8b,
        88        88  88           88           88           88           88    `8b            `8b
        88        88  88           88           88           88           88     `8b   Y8a     a8P
        88        88  88888888888  88888888888  88           88888888888  88      `8b   "Y88888P"
     */

    //=====SMART=====//

    public static function max($table, $col, $find = null)
    {
        self::init();

        $colname = 'MAX(' . $col . ')';
        $result = self::one($table, $find, null, 'ASC', null, [$colname]);

        if (!$result) {
            return 0;
        }

        return $result[$colname];
    }

    public static function sum($table, $columnToCount, $find = [1 => 1])
    {
        self::init();

        $colname = 'SUM(' . $columnToCount . ')';
        $result = self::one($table, $find, null, 'ASC', false, [$colname]);

        if (!$result) {
            return 0;
        }

        return $result[$colname];
    }

    public static function one(
        $table,
        $find = null,
        $sortBy = null,
        $ASC = "ASC",
        $abs = false,
        $columns = null,
        $groupby = null
    ) {
        foreach (self::retrieve($table, $find, $sortBy, $ASC, 1, $abs, $columns, $groupby) as $row) {
            return $row;
        }

        return [];
    }

    public static function numRows(string $table, array $find = [1 => 1], $distinct = null)
    {
        self::init();

        $column = 'COUNT(' . ($distinct ? "DISTINCT " . $distinct : "*") . ')';
        $counts = self::one($table, $find, null, "ASC", false, [$column]);

        if (!$counts) {
            return 0;
        }

        return $counts[$column];
    }

    //------STUPID-------//
    public static function columnNames(string $table)
    {
        return self::manualQuery("SHOW columns FROM `" . $table . "`");
    }

    public static function tableExists($table)
    {
        $finalQ = "SELECT * FROM `information_schema`.`TABLES` WHERE `TABLE_NAME` = :FINT && `TABLE_SCHEMA` = '" . DBNAME . "'";
        $findArray[':FINT'] = $table;
        $PDOEX = $findArray;

        $PreppedStatement = self::$connection->prepare($finalQ);
        $PreppedStatement->execute($PDOEX);

        if (($row = $PreppedStatement->fetch(\PDO::FETCH_ASSOC)) != false) {
            return true;
        }

        return false;
    }

    public static function manualQuery(string $manualQuery, $return = true, $oneRow = false)
    {
        self::init();
        self::runQuery($manualQuery);
        if (!$return) {
            return true;
        }

        while (($row = self::$PreppedStatement->fetch(\PDO::FETCH_ASSOC)) != false) {
            $rows[] = $row;
            if ($oneRow) {
                $rows = $row;
                break;
            }
        }

        self::$PreppedStatement->closeCursor();
        return $rows;
    }

    public static function search(
        $table,
        $ss,
        $find = null,
        array $ignoredCols = [],
        array $onlyCols = [],
        int $limit = 5,
        $sortBy = null,
        $ASC = "ASC"
    ) {
        self::init();

        array_walk(
            $ignoredCols,
            function (&$value, $key) {
                $value = strtolower($value);
            }
        );

        array_walk(
            $onlyCols,
            function (&$value, $key) {
                $value = strtolower($value);
            }
        );

        $searchString = [];
        $ssSearch[':searchString'] = "%" . $ss . "%";

        foreach (self::columnNames($table) as $in => $column) {
            $lowerCaseColumn = strtolower($column['Field']);
            if (in_array($lowerCaseColumn, $ignoredCols)) {
                continue;
            }

            if (sizeof($onlyCols) > 0 && !in_array($lowerCaseColumn, $onlyCols)) {
                continue;
            }

            $searchString[] = " (`" . $column['Field'] . "` LIKE :searchString) ";
        }

        $searchString = implode("OR", $searchString);

        $where = '';
        $PDOEX = $ssSearch;

        if ($find) {
            $where = self::buildWhere($find);
            $PDOEX = $where['find'] + $ssSearch;
            $where = implode(" AND ", $where['str']);
        }

        $orderBy = '';
        if ($sortBy) {
            $orderBy = self::buildOrder($sortBy, null, $ASC);
        }

        $limit = self::buildLimit($limit);

        $finalQ = "SELECT * FROM `" . $table . "` WHERE (" . $searchString . ") AND " . $where . $orderBy . $limit . ";";
        $PreppedStatement = self::$connection->prepare($finalQ);
        $PreppedStatement->execute($PDOEX);

        $rows = [];

        while (($row = $PreppedStatement->fetch(\PDO::FETCH_ASSOC)) != false) {
            $rows[] = $row;
        }

        return $rows;
    }

    /*
        88888888ba   88        88  88  88           88888888ba,    88888888888  88888888ba    ad88888ba
        88      "8b  88        88  88  88           88      `"8b   88           88      "8b  d8"     "8b
        88      ,8P  88        88  88  88           88        `8b  88           88      ,8P  Y8,
        88aaaaaa8P'  88        88  88  88           88         88  88aaaaa      88aaaaaa8P'  `Y8aaaaa,
        88""""""8b,  88        88  88  88           88         88  88"""""      88""""88'      `"""""8b,
        88      `8b  88        88  88  88           88         8P  88           88    `8b            `8b
        88      a8P  Y8a.    .a8P  88  88           88      .a8P   88           88     `8b   Y8a     a8P
        88888888P"    `"Y8888Y"'   88  88888888888  88888888Y"'    88888888888  88      `8b   "Y88888P"
     */

    public static function buildWhere(array $where)
    {
        if (!$where) {
            return ['str' => ["1 = 1"], 'find' => []];
        }

        $finalWhere = ['str' => [], 'find' => []];

        foreach ($where as $key => $value) {
            if ($key == '1' && $value == '1') {
                $finalWhere['str'][] = '1 = 1';
                continue;
            }

            $keyColumn = ' `' . $key . '` ';
            $keyPlaceholder = ':' . $key . 'W';


            if (is_array($value)) {
                $comparator = $value[0];
                $operator = $value[1];

                if (is_array($comparator)) {
                    $placeholders = [];

                    foreach ($comparator as $num => $possible) {
                        $finalWhere['find'][$keyPlaceholder . $num] = $possible;
                        $placeholders[] = $keyPlaceholder . $num;
                    }

                    $finalWhere['str'][] = $keyColumn . $operator . ' (' . implode(',', $placeholders) . ')';
                } elseif ($comparator == 'NOW()') {
                    $finalWhere['str'][] = $keyColumn . $operator . ' ' . $comparator;
                } else {
                    $finalWhere['find'][$keyPlaceholder] = $comparator;
                    $finalWhere['str'][] = $keyColumn . $operator . ' ' . $keyPlaceholder;
                }

                continue;
            }

            if (strtolower($value) == "null" || $value === null) {
                $finalWhere['str'][] = $keyColumn . ' IS NULL ';
                continue;
            }

            if (strtolower($value) == "notnull") {
                $finalWhere['str'][] = $keyColumn . ' IS NOT NULL ';
                continue;
            }

            $finalWhere['find'][$keyPlaceholder] = $value;
            $finalWhere['str'][] = $keyColumn . ' = ' . $keyPlaceholder;
        }

        return $finalWhere;
    }

    public static function buildUpdate(array $update)
    {
        $finalUpdate = ['str' => [], 'find' => []];

        foreach ($update as $key => $value) {
            $keyColumn = ' `' . $key . '` ';
            $keyPlaceholder = ':' . $key . 'U';

            if (strtolower($value) == "null" || $value === null) {
                $finalUpdate['str'][] = $keyColumn . ' = NULL ';
                continue;
            }

            $finalUpdate['find'][$keyPlaceholder] = $value;
            $finalUpdate['str'][] = $keyColumn . ' = ' . $keyPlaceholder;
        }

        return $finalUpdate;
    }

    public static function buildOrder($sort = null, $absolute = null, $ascending = null)
    {
        if (!$sort) {
            return '';
        }

        if (is_string($sort)) {
            if ($sort == 'RAND()') {
                return ' ORDER BY RAND() ';
            }

            $sort = [$sort];
            $ascending = [$ascending];
        }

        $sortby = [];

        foreach ($sort as $key => $sortVal) {
            $sortByValue = $sortVal;
            $ascendingValue = $ascending[$key];

            $sortby[] = ($absolute ? 'ABS(' : '') . '`' . $sortByValue . '`' . ($absolute ? ')' : '') . ' ' . $ascendingValue . ' ';
        }


        return ' ORDER BY ' . implode(',', $sortby);
    }

    public static function buildGroup($group = null)
    {
        return $group ? ' GROUP BY ' . $group : '';
    }

    public static function buildLimit($limit = null)
    {
        return $limit ? ' LIMIT ' . $limit : '';
    }

    public static function buildCols(array $columns = null)
    {
        return $columns ? implode(", ", $columns) : '*';
    }
}
