<?php 
    namespace App\Model\Requisition;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class RequisitionQueryHandler extends QueryHandler { 
        /**
         * `selectRequisition` Query string that will fetch requisition.
         * @return string
         */
        public function selectRequisition($id = false, $name = false)
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
                'R.has_pool',
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
                              ->where(['R.is_active' => ':is_active', 'R.created_by' => ':created_by']);


            $initQuery = ($id)   ? $initQuery->andWhere(['R.id' => ':id'])         : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhereLike(['R.name' => ':name']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

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
                'FORMAT(E.salary_range, 2) as salary_range',
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

        public function selectPositions($id = false)
        {
            $fields = [
                'P.id',
                'P.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('positions P')
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

        /**
         * `selectProjects` Query string that will select from table `projects`
         * @return string
         */
        public function selectProjects()
        {
            $fields = [
                'P.id',
                'P.project_code',
                'P.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }

         /**
         * `selectRequisitionNumbers`
         * @return string
         */
        public function selectRequisitionNumbers()
        {
            $fields = array(
                'RQ.id',
                'RQ.erf_no',
            );

            $initQuery = $this->select($fields)
                              ->from('requisitions RQ')
                              ->where(array('RQ.is_active' => ':is_active'))
                              ->orderBy('RQ.id', 'DESC')
                              ->limit(1);

            return $initQuery;
        }

        public function selectAllRQNumbers()
        {
            $fields = array(
                'RQ.id',
                'RQ.erf_no',
            );

            $initQuery = $this->select($fields)
                              ->from('requisitions RQ')
                              ->where(array('RQ.is_active' => ':is_active', 'RQ.erf_no' => ':erf_no'));

            return $initQuery;
        }

         /**
         * Undocumented function
         *
         * @param boolean $requisition_id
         * @return void
         */
        public function selectRqSignatories($requisition_id = false)
        {
            $fields = array(
                'RS.id',
                'RS.requisition_id',
                'RS.signatory_id',
                'RS.seq',
                'RS.is_approved',
                'IF(RS.remarks IS NULL, "", RS.remarks) as remarks',
                'DATE_FORMAT(RS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'EI.employee_no',
                'P.name as position_name',
                'D.name as department_name',
                'P.id as position_id'
            );

            $joins = array(
                'users U'                       =>      'U.id = RS.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('requisition_signatories RS')
                              ->leftJoin($joins)
                              ->where(array('RS.is_active' => ':is_active'));

            $initQuery = ($requisition_id) ? $initQuery->andWhere(array('RS.requisition_id' => ':requisition_id')) : $initQuery;

            return $initQuery;
        }

         /**
         * `selectPersonalInformations` Query string that will from table `personal_informations`.
         * @param  string $id
         * @return string
         */
        public function selectPersonalInformations($id = '')
        {
            $fields = [
                'PI.id',
                'U.id as user_id',
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'PI.sname',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'EI.id as ei_id',
                'EI.employee_no',
                'EI.head_id',
                'P.id as position_id',
                'P.name as position_name',
                'D.name as department_name',
                'D.id as department_id'
            ];

            $joins = [
                'users U'                    => 'PI.id = U.personal_information_id',
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P'                => 'EI.position_id = P.id',
                'departments D'              => 'P.department_id = D.id'
            ];

            $initQuery = $this->select($fields)
                              ->from('personal_informations PI')
                              ->join($joins)
                              ->where(['P.is_active' => ':is_active']);

            return $initQuery;
        }


        /**
         * `insertRequisition` Query string that will insert to table `requisition`
         * @return string
         */
        public function insertRequisition($data = [])
        {
            $initQuery = $this->insert('requisitions', $data);

            return $initQuery;
        }

        /**
         * `insertErPersonnel` Query string that will insert to table `er_personnels`
         * @return string
         */
        public function insertErPersonnel($data = [])
        {
            $initQuery = $this->insert('er_personnels', $data);

            return $initQuery;
        }

        /**
         * `updateRequisition` Query string that will update specific department information from table `requisition`
         * @return string
         */
        public function updateRequisition($id = '', $data = [])
        {
            $initQuery = $this->update('requisitions', $id, $data);

            return $initQuery;
        }

        /**
         * `updateErPersonnel` Query string that will update specific department information from table `er_personnels`
         * @return string
         */
        public function updateErPersonnel($id = '', $data = [])
        {
            $initQuery = $this->update('er_personnels', $id, $data);

            return $initQuery;
        }

        public function insertRqSignatories($data = [])
        {
            $initQuery = $this->insert('requisition_signatories', $data);

            return $initQuery;
        }


        /**
         * `deleteErPersonnel` Query string that will delete specific er personnel.
         * @param  boolean $id
         * @return string
         */
        public function deleteErPersonnel($id = false)
        {
            $initQuery = $this->delete('er_personnels')
                              ->where(['employee_requisition_id' => ':employee_requisition_id']);

            return $initQuery;
        }
    }