<?php

namespace App\Model\PrsApprovals;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class PrsApprovalsQueryHandler extends QueryHandler
{
    /**
     * `selectRequestTypes` Query string that will select from table `request_types`.
     * @param  boolean $id
     * @param  boolean $userId
     * @return string
     */
    public function selectRequestTypes($id = false, $userId = false)
    {
        $fields = array(
            'RT.id',
            'RT.name',
            'RT.cost_code',
            'RT.updated_by',
            'RT.updated_at',
            'RT.status'
        );

        $initQuery = $this->select($fields)
            ->from('request_types RT')
            ->where(array('RT.status' => ':status'));

        $initQuery = ($id) ? $initQuery->andWhere(array('RT.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectProjects` Query string that will select from table `projects`.
     * @param  boolean $id
     * @param  boolean $userId
     * @return string
     */
    public function selectProjects($id = false, $userId = false)
    {
        $fields = array(
            'P.id',
            'P.project_code',
            'P.name',
            'P.location',
            'P.longitude',
            'P.latitude',
            'P.is_on_going',
        );

        $joins = array(
            'p_wds PWDS' => 'PWDS.project_id = P.id',
        );

        $initQuery = $this->select($fields)
            ->from('projects P')
            ->join($joins)
            ->where(array('P.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('P.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * `selectDepartments` Query string that will select from table `departments`.
     * @param  boolean $id
     * @param  boolean $userId
     * @return string
     */
    public function selectDepartments($id = false, $userId = false)
    {
        $fields = array(
            'D.id',
            'D.code',
            'D.charging',
            'D.name',
        );

        $initQuery = $this->select($fields)
            ->from('departments D')
            ->where(array('D.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    /**
     * selectPrs
     *
     * @param boolean $id
     * @param boolean $userId
     * @return void
     */
    public function selectPrs($id = false)
    {
        $fields = array(
            'PR.id',
            'PR.project_id',
            'PR.department_id',
            'PR.request_type_id',
            'PR.signatories',
            'PR.status',
            'PR.for_cancelation',
            'PR.head_id',
        );

        $whereNotInCondition = [
            '25', // 16026
            '26', // 18029
            '27', // 18080
            '24', // 18SG-013
            '9',  // 19001
            '10', // 19002
            '11', // 19003
            '12', // 19004
            '28', // 19005
            '29', // 19006
            '30', // 19007
            '31', // 19008
            '32', // 19009
            '33', // 19010
            '34', // 19011
            '35', // 19012
            '38', // 19013
            '39', // 19015
            '1',  // 19SCDC001
            '7',  // Y03-001
            '4',  // TC-01126
            '2',  // TC-01147
            '3',  // TC01089
            '36', // 19SG-001
            '37', // 19SG-002
            '23', // 19SG-003
            '69', // 19SG-004
        ];

        $initQuery = $this->select($fields)
            ->from('purchase_requisitions PR')
            ->where(array('PR.is_active' => ':is_active'))
            ->andWhereNotIn('PR.project_id', $whereNotInCondition);

        $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;

        return $initQuery;
    }
}
