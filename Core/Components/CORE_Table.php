<?php

/**
 * Table View Class Construction
 *
 * @author Nick
 */
class CORE_Table
{

    public static function generate(
        $tableName,
        $tableData,
        $columns,
        $individualActions = array(),
        $bulkActions = array(),
        $options = array(),
        $hideOptions = true
    )
    {
        $data = new stdClass();
        $data->tableName = $tableName;
        $data->tableData = $tableData;
        $data->tableColumns = $columns;
        $data->tableOptions = $options;
        $data->tableIndividuals = $individualActions;
        $data->tableBulks = $bulkActions;
        $data->hideOptions = $hideOptions;

        return View::storeRequireIntoText("Core/Components/Views/CV_TableView.php", $data);
    }
}
