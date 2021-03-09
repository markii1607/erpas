<?php 
    namespace App\Model\RequisitionApproval;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class RequisitionApprovalQueryHandler extends QueryHandler { 
        
        public function selectSignatoryTransactions($id = false, $requisition_id = false, $is_approved = false)
        {
            $fields = array(
                'RS.id',
                'RS.requisition_id',
                'RS.signatory_id',
                'RS.queue',
                'RS.seq',
                'RS.is_approved',
                'IF(RS.remarks IS NULL, "", RS.remarks) as remarks',
                'DATE_FORMAT(RS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'R.erf_no',
                'R.department_id',
                'R.employee_id',
                'R.reason',
                'R.reason_replacement',
                'R.employment_status',
                'R.employment_status_contractual',
                'R.employment_status_project_based',
                'DATE_FORMAT(R.employment_status_project_based_date, "%m/%d/%Y") as employment_status_project_based_date',
                'R.employment_status_extra',
                'DATE_FORMAT(R.employment_status_extended, "%m/%d/%Y") as employment_status_extended',
                'R.employment_status_extended_reason',
                'R.qualifications_eb',
                'R.qualifications_we',
                'R.qualifications_pa',
                'R.qualifications_or',
                'R.requested_by',
                'DATE_FORMAT(R.created_at, "%m/%d/%Y") as created_at',
                'R.status',
                'R.has_pool',
                'R.created_by',
                'D.name as department_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'CONCAT(PRI.fname, " ", PRI.mname, " ", PRI.lname) as full_name',
                'PO.name as position_name',
                'P.id as project_id',
                'P.name as project_name',
                'P.project_code'
            );

            $leftjoins = array(
                'requisitions R'             =>    'R.id = RS.requisition_id',
                'projects P'                =>    'P.id  = R.project_id',
                'departments D'             =>    'R.department_id = D.id',
                'personal_informations PI'  =>    'PI.id = R.requested_by',
                'positions PO'              =>    'PO.id = PI.id',
                'personal_informations PRI' =>    'PRI.id = R.employee_id'
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_signatories RS')
                              ->leftJoin($leftjoins)
                              ->where(array('RS.is_active' => ':is_active', 'RS.signatory_id' => ':signatory', 'RS.queue' => ':queue'));

            $initQuery = ($id)          ? $initQuery->andWhere(array('RS.id' => ':id')) : $initQuery;
            $initQuery = ($requisition_id)      ? $initQuery->andWhere(array('RS.requisition_id' => ':requisition_id')) : $initQuery;
            $initQuery = ($is_approved) ? $initQuery->andWhere(array('RS.is_approved' => ':is_approved')) : $initQuery;

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

        public function selectRqSignatories($requisition_id = false)
        {
            $fields = array(
                'RS.id',
                'RS.requisition_id',
                'RS.signatory_id',
                'RS.queue',
                'RS.seq',
                'RS.is_approved',
                'DATE_FORMAT(RS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'IF(RS.remarks IS NULL, "", RS.remarks) as remarks',
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_signatories RS')
                              ->where(array('RS.is_active' => ':is_active'));

            $initQuery = ($requisition_id) ? $initQuery->andWhere(array('RS.requisition_id' => ':requisition_id')) : $initQuery;

            return $initQuery;
        }

        public function selectRequisitions($id = false, $name = false)
        {
            $fields = [
                'R.id',
                'R.erf_no',
                'R.department_id',
                'R.employee_id',
                'R.reason',
                'R.reason_replacement',
                'R.employment_status',
                'R.employment_status_contractual',
                'R.employment_status_project_based',
                'DATE_FORMAT(R.employment_status_project_based_date, "%m/%d/%Y") as employment_status_project_based_date',
                'R.employment_status_extra',
                'DATE_FORMAT(R.employment_status_extended, "%m/%d/%Y") as employment_status_extended',
                'R.employment_status_extended_reason',
                'R.qualifications_eb',
                'R.qualifications_we',
                'R.qualifications_pa',
                'R.qualifications_or',
                'R.requested_by',
                'DATE_FORMAT(R.created_at, "%m/%d/%Y") as created_at',
                'R.status',
                'D.name as department_name',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                'CONCAT(PRI.fname, " ", PRI.mname, " ", PRI.lname) as full_name',
                'PO.name as position_name',
                'P.id as project_id',
                'P.name as project_name',
                'P.project_code'
                
            ];

            $initQuery = $this->select($fields)
                              ->from('requisitions R')
                              ->leftJoin(['users U'=> 'U.id = R.created_by','departments D'=> 'D.id = R.department_id', 'personal_informations PI'=>'PI.id = R.requested_by', 'positions PO'=>'PO.id = PI.id', 'personal_informations PRI'=>'PRI.id = R.employee_id'])
                              ->leftJoin(['projects P' => 'P.id = R.project_id'])
                              ->where(['R.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['R.id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['R.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        public function selectSpecificSignatory($requisition_id = false, $seq = false)
        {
            $fields = array(
                'RS.id',
                'RS.requisition_id',
                'RS.seq'
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_signatories RS')
                              ->where(array('RS.is_active' => ':is_active'));
                            //   ->orderBy('RS.seq', 'DESC')
                            //   ->limit(1);

            $initQuery = ($requisition_id) ? $initQuery->andWhere(array('RS.requisition_id' => ':requisition_id')) : $initQuery;
            $initQuery = ($seq) ? $initQuery->andWhere(array('RS.seq' => ':seq')) : $initQuery;

            return $initQuery;
        }

        public function selectEmployee($id = false, $name = false, $erId = false)
        {
            $fields = [
                'E.id',
                'E.employee_requisition_id',
                'DATE_FORMAT(E.date_needed, "%m/%d/%Y") as date_needed',
                'E.no_of_employee',
                'E.position_id',
                'E.salary_range',
                'E.names',
                'E.remarks',
                'P.name as position_name',
                // 'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as requested_by',
                // '"" as status'

            ];

            $initQuery = $this->select($fields)
                              ->from('er_personnels E')
                              ->join(['positions P'=> 'P.id = E.position_id'])
                              ->where(['E.is_active' => ':is_active']);


            $initQuery = ($id)   ? $initQuery->andWhere(['E.employee_requisition_id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['E.name' => ':name']) : $initQuery;
            $initQuery = ($erId) ? $initQuery->andWhere(['E.employee_requisition_id' => ':employee_requisition_id']) : $initQuery;

            return $initQuery;
        }

        public function updateRqReport($id, $data = array())
        {
            $initQuery = $this->update('requisitions', $id, $data);

            return $initQuery;
        }

        public function updateRqSignatory($id, $data = array())
        {
            $initQuery = $this->update('requisition_signatories', $id, $data);

            return $initQuery;
        }
    }