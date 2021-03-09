<?php

namespace App\Model\MemorandumReceiptMonitoring;

require_once('../../AbstractClass/QueryHandler.php');

use App\AbstractClass\QueryHandler;

class MemorandumReceiptMonitoringQueryHandler extends QueryHandler {

    public function selectMemorandumReceipts($id = false)
    {
        $fields = [
            'MR.id',
            'MR.user_id',
            'MR.signatory_id',
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

        $initQuery = ($id) ? $initQuery->andWhere(['MR.id' => ':id']) : $initQuery;

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
            '(SELECT unit FROM material_units WHERE id = MRI.unit_id) as unit',
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

    public function selectAllMrItems()
    {
        $fields = [
            'MI.id',
            'MI.mr_id',
            'MI.unit_id',
            'MI.project_id',
            'MI.department_id',
            'DATE_FORMAT(MI.date, "%M %d, %Y") as date',
            'DATE_FORMAT(MI.date_acquired, "%M %d, %Y") as date_acquired',
            'MI.qty',
            '(SELECT unit FROM material_units WHERE id = MI.unit_id) as unit',
            'MI.particulars',
            'MI.serial_no',
            'MI.unit_cost',
            'MI.acquisition_cost',
            'MI.prs_no',
            'MI.status',
            'MI.remarks',
            'MI.is_released',
            'MI.released_by',
            'DATE_FORMAT(MI.released_at, "%M %d, %Y %r") as released_at',
            'MR.user_id',
            'MR.project_id as mr_proj_id',
            'MR.department_id as mr_dept_id',
            'MR.mr_no',
            'MR.status as mr_status',
        ];

        $initQuery = $this->select($fields)
                          ->from('mr_items MI')
                          ->join(['mr_memorandum_receipts MR' => 'MR.id = MI.mr_id'])
                          ->where(['MI.is_active' => ':is_active']);


        return $initQuery;
    }

    public function selectMrSignatories($mr_id = false)
    {
        $fields = [
            'MRS.id',
            'MRS.mr_id',
            'MRS.signatory_id',
            'MRS.signatory_approval',
            'DATE_FORMAT(MRS.approved_at, "%M %d, %Y %r") as approved_at',
            'MRS.remarks',
        ];

        $initQuery = $this->select($fields)
                          ->from('mr_signatories MRS')
                          ->where(['is_active' => ':is_active']);

        $initQuery = ($mr_id) ? $initQuery->andWhere(['MRS.mr_id' => ':mr_id']) : $initQuery;

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
                            ->where(array('U.is_active' => ':is_active'));

        $initQuery = ($id) ? $initQuery->andWhere(array('U.id' => ':id')) : $initQuery;

        return $initQuery;
    }

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

    public function insertMrSignatories($data = [])
    {
        $initQuery = $this->insert('mr_signatories', $data);

        return $initQuery;
    }

    public function updateMemorandumReceipts($id = '', $data = [])
    {
        $initQuery = $this->update('mr_memorandum_receipts', $id, $data);

        return $initQuery;
    }
}