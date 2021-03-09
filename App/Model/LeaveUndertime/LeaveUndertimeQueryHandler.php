<?php
    namespace App\Model\LeaveUndertime;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class LeaveUndertimeQueryHandler extends QueryHandler {
        /**
         * `selectLeaveUndertimes` Query string that will fetch overtime.
         * @return string
         */
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
                              ->leftJoin($leftJoins)
                              ->where(['LU.is_active' => ':is_active', 'LU.created_by' => ':created_by']);

            $initQuery = ($id) ? $initQuery->andWhere(['LU.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectEmployeeCredits` Query string that will fetch deduction.
         * @return string
         */
        public function selectEmployeeCredits($ecId = false, $name = false)
        {
            $fields = [
                'EC.id',
                'EC.personal_information_id',
                'EC.leave_type_id',
                'EC.credit_value',
                'EC.used_credit',
                '(EC.credit_value - EC.used_credit) as balance_credit',
                'LT.name'
            ];

            $joins = [
                'leave_types LT' => 'LT.id = EC.leave_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('employee_credits EC')
                              ->join($joins)
                              ->where(['EC.is_active' => ':is_active']);

            $initQuery = ($ecId) ? $initQuery->andWhere(['EC.id' => ':id']) : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhere(['EC.personal_information_id' => ':personal_information_id']) : $initQuery;

            return $initQuery;
        }

        /**
         * Undocumented function
         *
         * @param boolean $leave_undertime_id
         * @return void
         */
        public function selectLusSignatories($leave_undertime_id = false)
        {
            $fields = array(
                'LUS.id',
                'LUS.leave_undertime_id',
                'LUS.signatory_id',
                'LUS.seq',
                'LUS.is_approved',
                'IF(LUS.remarks IS NULL, "", LUS.remarks) as remarks',
                'DATE_FORMAT(LUS.updated_at, "%M %d, %Y %h:%i:%s %p") as date_approved',
                'CONCAT(PI.lname, ", ", PI.fname, " ", PI.mname) as full_name',
                'EI.employee_no',
                'P.name as position_name',
                'D.name as department_name'
            );

            $joins = array(
                'users U'                       =>      'U.id = LUS.signatory_id',
                'personal_informations PI'      =>      'PI.id = U.personal_information_id',
                'employment_informations EI'    =>      'EI.personal_information_id = PI.id',
                'positions P'                   =>      'P.id = EI.position_id',
                'departments D'                 =>      'D.id = P.department_id'
            );

            $initQuery = $this->select($fields)
                              ->from('leave_undertime_signatories LUS')
                              ->leftJoin($joins)
                              ->where(array('LUS.is_active' => ':is_active'));

            $initQuery = ($leave_undertime_id) ? $initQuery->andWhere(array('LUS.leave_undertime_id' => ':leave_undertime_id')) : $initQuery;

            return $initQuery;
        }

        public function selectLeaveTypes($id = false)
        {
            $fields = [
                'LT.id',
                'LT.name',
            ];

            $initQuery = $this->select($fields)
                              ->from('leave_types LT')
                              ->where(['LT.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['LT.id' => ':id']) : $initQuery;

            return $initQuery;
        }


   /**
         * `selectProjects` Query string that will fetch from table `projects`.
         * @param  boolean $id
         * @return string
         */
        public function selectProjects($id = false)
        {
            $fields = [
                'P.id',
                'P.project_code as charging',
                'P.name',
                '"P" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('projects P')
                              ->where(['P.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['P.id' => ':id']) : $initQuery;

            return $initQuery;
        }

        /**
         * `selectDepartments` Query string that will fetch from table `departments`.
         * @param  boolean $id
         * @return string
         */
        public function selectDepartments($id = false)
        {
            $fields = [
                'D.id',
                'D.charging',
                'D.name',
                '"D" as pd_type'
            ];

            $initQuery = $this->select($fields)
                              ->from('departments D')
                              ->where(['D.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['D.id' => ':id']) : $initQuery;

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
                'PI.fname',
                'PI.mname',
                'PI.lname',
                'PI.sname',
                'CONCAT(PI.fname, " ", PI.mname, " ", PI.lname) as full_name',
                'EI.id as ei_id',
                'EI.employee_no',
                'P.id as position_id',
                'P.name as position_name',
                'D.name as department_name',
                'D.id as department_id'
            ];

            $joins = [
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
         * selectEmployees
         *
         * @return void
         */
        public function selectEmployees($id = false, $department_id = false)
        {
            $fields = array(
                'PI.id',
                'EI.position_id',
                'P.department_id',
                'P.name as position_name',
                'D.charging',
                'D.name as department_name',
                'CONCAT(PI.lname,", ",PI.fname," ",PI.mname) as fullname',
            );

            $join = array(
                'employment_informations EI' => 'PI.id = EI.personal_information_id',
                'positions P' => 'EI.position_id = P.id',
                'departments D' => 'P.department_id = D.id'
            );

            $initQuery = $this->select($fields)
                ->from('personal_informations PI')
                ->leftJoin($join)
                ->where(array('PI.is_active' => ':status'));

            $initQuery = ($id) ? $initQuery->andWhere(array('PI.id' => ':id')) : $initQuery;
            $initQuery = ($department_id) ? $initQuery->andWhere(array('P.department_id' => ':department_id')) : $initQuery;

            return $initQuery;
        }

        /**
         * selectSignatories
         *
         * @param boolean $id
         * @return void
         */
        // public function selectSignatories($id = false)
        // {
        //     $fields = array(
        //         'OS.id',
        //         'OS.leave_undertime_id',
        //         'OS.signatory_id',
        //         'OS.seq',
        //         'OS.queue',
        //         'OS.is_approved',
        //         'OS.remarks',
        //     );

        //     $initQuery = $this->select($fields)
        //         ->from('overtime_signatories OS')
        //         ->where(array('OS.status' => ':status'));

        //     $initQuery = ($id) ? $initQuery->andWhere(array('OS.menu_id' => ':id')) : $initQuery;

        //     return $initQuery;
        // }


        /**
         * `insertLeaveUndertime` Query string that will insert to table `leave_undertimes`
         * @return string
         */
        public function insertLeaveUndertime($data = [])
        {
            $initQuery = $this->insert('leave_undertimes', $data);

            return $initQuery;
        }

        public function insertLuSignatories($data = [])
        {
            $initQuery = $this->insert('leave_undertime_signatories', $data);

            return $initQuery;
        }

        /**
         * `updateLeaveUndertime` Query string that will update specific overtime information from table `overtime`
         * @return string
         */
        public function updateLeaveUndertime($id = '', $data = [])
        {
            $initQuery = $this->update('leave_undertimes', $id, $data);

            return $initQuery;
        }
    }