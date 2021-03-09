<?php

    namespace App\Model\MyMemorandumReceipt;

    require_once('../../AbstractClass/QueryHandler.php');

    use App\AbstractClass\QueryHandler;

    class MyMemorandumReceiptQueryHandler extends QueryHandler {

        public function selectMrItems($id = '')
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
                'MI.is_confirmed',
                'MI.confirmed_by',
                'DATE_FORMAT(MI.confirmed_at, "%M %d, %Y %r") as confirmed_at',
                'MR.user_id',
                'MR.mr_no',
                'MR.status as mr_status',
                'IF(MI.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(MI.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(MI.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
            ];

            $leftJoins = [
                'projects P'    => 'P.id = MI.project_id',
                'departments D' => 'MI.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('mr_items MI')
                              ->join(['mr_memorandum_receipts MR' => 'MR.id = MI.mr_id'])
                              ->leftJoin($leftJoins)
                              ->where(['MI.is_active' => ':is_active', 'MR.status' => ':status', 'MR.user_id' => ':user_id', 'MI.is_released' => ':is_released']);

            $initQuery = ($id) ? $initQuery->andWhere(['MI.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectMrPersonal($mr_id = false)
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
                'CONCAT_WS(" ", NULLIF(PI.fname, ""), NULLIF(CONCAT(LEFT(PI.mname,1),"."), ""), NULLIF(PI.lname, "")) as full_name',
                'NULLIF(EI.is_department_head, "") as is_department_head'
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

        public function updateMrItems($id = '', $data = [])
        {
            $initQuery = $this->update('mr_items', $id, $data);

            return $initQuery;
        }
    }