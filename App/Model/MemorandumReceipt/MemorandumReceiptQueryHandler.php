<?php

namespace App\Model\MemorandumReceipt;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class MemorandumReceiptQueryHandler extends QueryHandler {

    public function selectDepartments($id = false) {
        $fields = array(
            'D.id',
            'D.charging as charging',
            'D.name',
            '"Department" as type'
        );

        $initQuery = $this->select($fields)
                            ->from('departments D')
                            ->where(array('D.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('D.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectProjects($id = false) {
        $fields = array(
            'PR.id',
            'PR.project_code as charging',
            'PR.name',
            '"Project" as type',
        );

        $initQuery = $this->select($fields)
                            ->from('projects PR')
                            ->where(array('PR.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('PR.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectUsers($id = false)
    {
        $fields = array(
            'U.id as user_id',
            'PI.fname',
            'PI.mname',
            'PI.lname',
            'EI.employee_no',
            'DATE_FORMAT(EI.date_hired, "%M %d, %Y") as date_hired',
            'P.id as position_id',
            'P.name as position',
            'D.id as department_id',
            'D.name as department',
            'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(CONCAT(LEFT(PI.mname,1),"."), ""), NULLIF(PI.lname, "")) as full_name'
        );

        $joins = array(
            'personal_informations PI'   => 'U.personal_information_id = PI.id',
            'employment_informations EI' => 'PI.id = EI.personal_information_id',
            'positions P'                => 'EI.position_id = P.id',
            'departments D'              => 'P.department_id = D.id'
        );

        $initQuery = $this->select($fields)
                            ->from('users U')
                            ->join($joins)
                            ->where(array('U.is_active' => ':is_active'))
                            ->andWhereNotEqual(array('U.id' => 1));

        $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectUnits($id = false) {
        $fields = array(
            'MU.id',
            'MU.unit',
        );

        $initQuery = $this->select($fields)
                            ->from('material_units MU')
                            ->where(array('MU.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('MU.id' => ':id')) : $initQuery;

        return $initQuery;
    }

    public function selectMrNo() {
        $initQuery = $this->logicEx('SELECT MAX(mr_no) as mr_no FROM mr_memorandum_receipts');

        return $initQuery;
    }

    public function selectMemorandumReceipts($id = false, $user_id = false, $project_id = false, $dept_id = false, $mr_no = false)
    {
        $fields = [
            'MR.id',
            'MR.user_id',
            'MR.accountable_officer_id',
            'MR.project_id',
            'MR.department_id',
            'MR.mr_no',
            'MR.remarks',
            'MR.status',
        ];

        $initQuery = $this->select($fields)
                          ->from('mr_memorandum_receipts MR')
                          ->where(['MR.is_active' => ':is_active']);

        $initQuery = ($id)          ?   $initQuery->andWhere(['MR.id' => ':id'])                    :   $initQuery;
        $initQuery = ($user_id)     ?   $initQuery->andWhere(['MR.user_id' => ':user_id'])          :   $initQuery;
        $initQuery = ($project_id)  ?   $initQuery->andWhere(['MR.project_id' => ':project_id'])    :   $initQuery;
        $initQuery = ($dept_id)     ?   $initQuery->andWhere(['MR.department_id' => ':dept_id'])    :   $initQuery;
        $initQuery = ($mr_no)       ?   $initQuery->andWhere(['MR.mr_no' => ':mr_no'])              :   $initQuery;

        return $initQuery;
    }

    public function selectMrItems($mr_id = false)
    {
        $fields = [
            'MRI.id',
            'MRI.mr_id',
            'MRI.unit_id',
            'MRI.project_id',
            'MRI.department_id',
            'DATE_FORMAT(MRI.date, "%M %d, %Y") as date',
            'MRI.qty',
            'MRI.particulars',
            'MRI.serial_no',
            'DATE_FORMAT(MRI.date_acquired, "%M %d, %Y") as date_acquired',
            'MRI.unit_cost',
            'MRI.acquisition_cost',
            'MRI.prs_no',
            'MRI.status',
            'MRI.remarks',
            '"saved" as data_status'
        ];

        $initQuery = $this->select($fields)
                          ->from('mr_items MRI')
                          ->where(['MRI.is_active' => ':is_active']);

        $initQuery = ($mr_id) ? $initQuery->andWhere(['MRI.mr_id' => ':mr_id']) : $initQuery;

        return $initQuery;
    }

    public function insertMemorandumReceipts($data = [])
    {
        $initQuery = $this->insert('mr_memorandum_receipts', $data);

        return $initQuery;
    }

    public function insertMrItems($data = [])
    {
        $initQuery = $this->insert('mr_items', $data);

        return $initQuery;
    }

    public function updateMemorandumReceipts($id = '', $data = [])
    {
        $initQuery = $this->update('mr_memorandum_receipts', $id, $data);

        return $initQuery;
    }

    public function updateMrItems($id = '', $data = [])
    {
        $initQuery = $this->update('mr_items', $id, $data);

        return $initQuery;
    }

    public function insertMrItemsRev($data = [])
    {
        $initQuery = $this->insert('mr_revisions', $data);

        return $initQuery;
    }
}