<?php 
    namespace App\Model\EmployeeCredits;

    require_once("../../AbstractClass/QueryHandler.php");

    use App\AbstractClass\QueryHandler;

    class EmployeeCreditsQueryHandler extends QueryHandler { 
        /**
         * `selectEmployeeCredits` Query string that will fetch deduction.
         * @return string
         */
        public function selectEmployeeCredits($id = false, $name = false)
        {
            $fields = [
                'EC.id',
                'EC.personal_information_id',
                'EC.leave_type_id',
                'EC.credit_value',
                'LT.name'
            ];

            $joins = [
                'leave_types LT' => 'LT.id = EC.leave_type_id'
            ];

            $initQuery = $this->select($fields)
                              ->from('employee_credits EC')
                              ->join($joins)
                              ->where(['EC.is_active' => ':is_active']);

            $initQuery = ($id) ? $initQuery->andWhere(['EC.id' => ':id']) : $initQuery;
            $initQuery = ($name) ? $initQuery->andWhere(['EC.personal_information_id' => ':personal_information_id']) : $initQuery;

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
         * `selectDepartments` Query String that will select from table `departments`
         * @return string
         */
        // public function selectDepartments($id = false)
        // {
        //     $fields = [
        //         'D.id',
        //         'D.charging',
        //     ];

        //     $initQuery = $this->select($fields)
        //                       ->from('departments D')
        //                       ->where(['D.is_active' => ':is_active']);

        //     return $initQuery;
        // }

        /**
         * `insertEmployeeCredits` Query string that will insert to table `employee_credits`
         * @return string
         */
        public function insertEmployeeCredits($data = [])
        {
            $initQuery = $this->insert('employee_credits', $data);

            return $initQuery;
        }

        /**
         * `updateEmployeeCredits` Query string that will update specific department information from table `requisition`
         * @return string
         */
        public function updateEmployeeCredits($id = '', $data = [])
        {
            $initQuery = $this->update('employee_credits', $id, $data);

            return $initQuery;
        }

         /**
         * `insertEcCredits` Query string that will insert to table `ec_credits`
         * @return string
         */
        // public function insertEcCredits($data = [])
        // {
        //     $initQuery = $this->insert('ec_credits', $data);

        //     return $initQuery;
        // }

         /**
         * `updateErPersonnel` Query string that will update specific department information from table `er_personnels`
         * @return string
         */
        // public function updateEcCredits($id = '', $data = [])
        // {
        //     $initQuery = $this->update('ec_credits', $id, $data);

        //     return $initQuery;
        // }

        /**
         * `deleteEcCredits` Query string that will delete specific er personnel.
         * @param  boolean $id
         * @return string
         */
        public function deleteEcCredits($id = false)
        {
            $initQuery = $this->delete('ec_credits')
                              ->where(['employee_requisition_id' => ':employee_requisition_id']);

            return $initQuery;
        }

    }