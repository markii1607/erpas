<?php 
    namespace App\Model\LeaveUndertimeApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class LeaveUndertimeApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $leave_undertime_id = false, $is_approved = false)
        {
            $fields = array(
                'LUS.id',
                'LUS.leave_undertime_id',
                'LUS.signatory_id',
                'LUS.queue',
                'LUS.seq',
                'LUS.is_approved',
                'IF(LUS.remarks IS NULL, "", LUS.remarks) as remarks',
                'DATE_FORMAT(LUS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'IF(LU.department_id IS NULL, "P", "D") as charging_type',
                'IF(LU.department_id IS NULL, LU.project_id, LU.department_id) as charging_id',
                'LU.type',
                'LU.leave_type_id',
                'LU.leave_status',
                'DATE_FORMAT(LU.date_from, "%m/%d/%Y") as date_from',
                'DATE_FORMAT(LU.date_to, "%m/%d/%Y") as date_to',
                'DATE_FORMAT(LU.time_from, "%h:%i %p") as time_from',
                'DATE_FORMAT(LU.time_to, "%h:%i %p") as time_to',
                'LU.hours_days',
                'LU.remarks_reason',
                'LU.status',
                'LU.created_by',
                'LT.name as leave_type_name',
                'IF(LU.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(LU.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(LU.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
                'DATE_FORMAT(LU.created_at, "%m/%d/%Y") as date_filed'
            );

            $leftjoins = array(
                'leave_undertimes LU'   => 'LU.id = LUS.leave_undertime_id',
                'projects P'            => 'P.id  = LU.project_id',
                'departments D'         => 'LU.department_id = D.id',
                'leave_types LT'        => 'LT.id = LU.leave_type_id'
            );

            $initQuery = $this->select($fields)
                              ->from('leave_undertime_signatories LUS')
                              ->leftJoin($leftjoins)
                              ->where(array('LUS.is_active' => ':is_active', 'LUS.signatory_id' => ':signatory', 'LUS.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('LUS.id' => ':id')) : $initQuery;
            $initQuery = ($leave_undertime_id)      ? $initQuery->andWhere(array('LUS.leave_undertime_id' => ':leave_undertime_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('LUS.is_approved' => ':is_approved')) : $initQuery;

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

        public function selectLuSignatories($leave_undertime_id = false)
        {
            $fields = array(
                'LUS.id',
                'LUS.leave_undertime_id',
                'LUS.signatory_id',
                'LUS.queue',
                'LUS.seq',
                'LUS.is_approved',
                'DATE_FORMAT(LUS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'IF(LUS.remarks IS NULL, "", LUS.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('leave_undertime_signatories LUS')
                              ->where(array('LUS.is_active' => ':is_active'));

            $initQuery = ($leave_undertime_id) ? $initQuery->andWhere(array('LUS.leave_undertime_id' => ':leave_undertime_id')) : $initQuery;

            return $initQuery;
        }

        public function selectLeaveUndertimes($id = false)
        {
            $fields = [
                'LU.id',
                'LU.department_id',
                'LU.project_id',
                'IF(LU.department_id IS NULL, "P", "D") as charging_type',
                'IF(LU.department_id IS NULL, LU.project_id, LU.department_id) as charging_id',
                'LU.type',
                'LU.leave_type_id',
                'LU.leave_status',
                'DATE_FORMAT(LU.date_from, "%m/%d/%Y") as date_from',
                'DATE_FORMAT(LU.date_to, "%m/%d/%Y") as date_to',
                'LU.time_from',
                'LU.time_to',
                'LU.hours_days',
                'LU.remarks_reason',
                'LU.status',
                'LT.name as leave_type_name',
                'IF(LU.project_id IS NULL, D.charging, P.project_code) as charging_code',
                'IF(LU.project_id IS NULL, D.name, P.name) as charging_name',
                'IF(LU.project_id IS NULL, CONCAT(D.charging, "-",(D.name)), CONCAT(P.project_code,"-", P.name)) as charging',
                'DATE_FORMAT(LU.created_at, "%m/%d/%Y") as created_at'
            ];

            $leftJoins = [
                'projects P'    => 'P.id = LU.project_id',
                'departments D' => 'LU.department_id = D.id',
                'leave_types LT'=> 'LT.id = LU.leave_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('leave_undertimes LU')
                            //   ->join(['departments D'=> 'D.id = LU.department_id',])
                              ->leftJoin($leftJoins)
                              ->where(['LU.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['LU.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        public function selectSpecificSignatory($leave_undertime_id = false, $seq = false)
        {
            $fields = array(
                'LUS.id',
                'LUS.leave_undertime_id',
                'LUS.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('leave_undertime_signatories LUS')
                              ->where(array('LUS.is_active' => ':is_active'));
                            //   ->orderBy('LUS.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($leave_undertime_id) ? $initQuery->andWhere(array('LUS.leave_undertime_id' => ':leave_undertime_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('LUS.seq' => ':seq')) : $initQuery;

            return $initQuery;
        }

        public function updateLuReport($id, $data = array())
        {
            $initQuery = $this->update('leave_undertimes', $id, $data);

            return $initQuery;
        }

        public function updateLuSignatory($id, $data = array())
        {
            $initQuery = $this->update('leave_undertime_signatories', $id, $data);

            return $initQuery;
        }
    }