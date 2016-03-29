<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2016/1/15
 * Time: 19:28
 */
class AbReportController extends AbBaseController
{
    public function createAction()
    {
        $tableNames = $this->tableNames();
        $data = array('table_names' => $tableNames);

        $views = [
            ["name" =>'新建报表', "template"=> "abreport/create"] ];

        parent::addDialog('添加列', 'abreport/dialog-new-row');
        parent::showTabViews($views, '创建报表', $data);
    }

    private function tableNames() {
        $a = $this->db->fetchAll("SHOW tables");
        $tableNames = array();
        foreach ($a as $table) {
            array_push($tableNames, $table['Tables_in_badmin']);
        }
        return $tableNames;
    }
}