<?php 
    namespace App\Model\OvertimeApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class OvertimeApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $overtime_id = false, $is_approved = false)
        {
            $fields = array(
                'OS.id',
                'OS.overtime_id',
                'OS.signatory_id',
                'OS.queue',
                'OS.seq',
                'OS.is_approved',
                'IF(OS.remarks IS NULL, "", OS.remarks) as remarks',
                'DATE_FORMAT(OS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'DATE_FORMAT(O.created_at, "%M %d, %Y") as date_filed',
                'DATE_FORMAT(O.date_of_ot, "%M %d, %Y") as date_of_ot',
                'IF(O.department_id IS NULL, "P", "D") as charging_type',
                'IF(O.department_id IS NULL, O.project_id, O.department_id) as charging_id',
                // 'O.time_from',
                // 'O.time_to',
                'DATE_FORMAT(O.time_from, "%h:%i %p") as time_from',
                'DATE_FORMAT(O.time_to, "%h:%i %p") as time_to',
                'O.total_hours',
                // 'CONCAT(from), "-",to) as overtime_hour',
                'O.reason',
                'O.status',
                'O.created_by',
                'IF(O.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(O.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(O.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
            );

            $leftjoins = array(
                'overtimes O'       =>    'O.id = OS.overtime_id',
                'projects P'          =>    'P.id  = O.project_id',
                'departments D'       =>    'O.department_id = D.id',
            );

            $initQuery = $this->select($fields)
                              ->from('overtime_signatories OS')
                              ->leftJoin($leftjoins)
                              ->where(array('OS.is_active' => ':is_active', 'OS.signatory_id' => ':signatory', 'OS.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('OS.id' => ':id')) : $initQuery;
            $initQuery = ($overtime_id)      ? $initQuery->andWhere(array('OS.overtime_id' => ':overtime_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('OS.is_approved' => ':is_approved')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployeeDetails($id = false, $user_id = false)
        {
            $fields = array(
                'EI.id',
                'EI.employee_no',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as fullname',
                'P.name as position',
                'D.name as department',
                'U.id as user_id'
            );

            $joins = array(
                'personal_informations PI'  =>  'PI.id = EI.personal_information_id',
                'users U'                   =>  'U.personal_information_id = PI.id',
                'positions P'               =>  'P.id = EI.position_id',
                'departments D'             =>  'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('employment_informations EI')
                              ->join($joins)
                              ->where(array('PI.is_active' => ':is_active'));

            $initQuery = ($id)  ? $initQuery->andWhere(array('EI.id' => ':id')) : $initQuery;
            $initQuery = ($user_id)  ? $initQuery->andWhere(array('U.id' => ':user_id')) : $initQuery;

            return $initQuery;
        }

        public function selectOtSignatories($overtime_id = false)
        {
            $fields = array(
                'OS.id',
                'OS.overtime_id',
                'OS.signatory_id',
                'OS.queue',
                'OS.seq',
                'OS.is_approved',
                'DATE_FORMAT(OS.updated_at, "%M %d, %Y %H:%i:%s %p") as date_approved',
                'IF(OS.remarks IS NULL, "", OS.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('overtime_signatories OS')
                              ->where(array('OS.is_active' => ':is_active'));

            $initQuery = ($overtime_id) ? $initQuery->andWhere(array('OS.overtime_id' => ':overtime_id')) : $initQuery;

            return $initQuery;
        }

        public function selectOvertimes($id = false)
        {
            $fields = [
                'O.id',
                'O.department_id',
                'O.project_id',
                'DATE_FORMAT(O.date_of_ot, "%m/%d/%Y") as date_of_ot',
                'IF(O.department_id IS NULL, "P", "D") as charging_type',
                'IF(O.department_id IS NULL, O.project_id, O.department_id) as charging_id',
                'DATE_FORMAT(O.time_from, "%h:%i %p") as time_from',
                'DATE_FORMAT(O.time_to, "%h:%i %p") as time_to',
                'O.total_hours',
                'O.reason',
                'O.status',
                'IF(O.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(O.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(O.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
                'DATE_FORMAT(O.created_at, "%m/%d/%Y") as created_at'
            ];

            $leftJoins = [
                'projects P'    => 'P.id = O.project_id',
                'departments D' => 'O.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('overtimes O')
                            //   ->join(['departments D'=> 'D.id = O.department_id',])
                              ->leftJoin($leftJoins)
                              ->where(['O.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['O.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSpecificSignatory($overtime_id = false, $seq = false)
        {
            $fields = array(
                'OS.id',
                'OS.overtime_id',
                'OS.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('overtime_signatories OS')
                              ->where(array('OS.is_active' => ':is_active'));
                            //   ->orderBy('OS.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($overtime_id) ? $initQuery->andWhere(array('OS.overtime_id' => ':overtime_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('OS.seq' => ':seq')) : $initQuery;

            return $initQuery;
        }

        public function updateOtReport($id, $data = array())
        {
            $initQuery = $this->update('overtimes', $id, $data);

            return $initQuery;
        }

        public function updateOtSignatory($id, $data = array())
        {
            $initQuery = $this->update('overtime_signatories', $id, $data);

            return $initQuery;
        }
    }